<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BatchController extends Controller
{
    /**
     * Display inventory batch list with search, filter, and risk levels.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $facilityId = $user->facility_id;

        if (($user->isDinkesAuditor() || $user->role === 'SYSADMIN') && $request->filled('facility_id')) {
            $facilityId = (int) $request->get('facility_id');
        }

        $facility = Facility::find($facilityId);

        $query = Batch::with(['medicine', 'facility']);

        if ($facilityId && ! $user->isDinkesAuditor()) {
            $query->where('facility_id', $facilityId);
        } elseif ($request->filled('facility_id')) {
            $query->where('facility_id', $request->get('facility_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                    ->orWhere('storage_location', 'like', "%{$search}%")
                    ->orWhereHas('medicine', function ($mq) use ($search) {
                        $mq->where('name', 'like', "%{$search}%")
                            ->orWhere('generic_name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $statsBatches = (clone $query)->get();
        $totalBatchesCount = $statsBatches->count();
        $totalStockUnits = (int) $statsBatches->sum('current_stock');
        $atRiskCount = $statsBatches->filter(fn ($b) => in_array($b->risk_level, ['CRITICAL', 'HIGH']))->count();
        $totalWasteValue = (float) $statsBatches->sum(fn ($b) => $b->projected_waste * (float) $b->unit_price_idr);

        $batches = $query->orderBy('expiry_date', 'asc')->paginate(15)->withQueryString();

        $facilitiesList = Facility::where('type', '!=', 'DINKES')->get();

        return view('batches.index', [
            'batches' => $batches,
            'facility' => $facility,
            'facilitiesList' => $facilitiesList,
            'user' => $user,
            'currentSearch' => $request->get('search', ''),
            'currentStatus' => $request->get('status', ''),
            'totalBatchesCount' => $totalBatchesCount,
            'totalStockUnits' => $totalStockUnits,
            'atRiskCount' => $atRiskCount,
            'totalWasteValue' => $totalWasteValue,
        ]);
    }

    /**
     * Show form to input a new pharmaceutical batch.
     */
    public function create(): View
    {
        $user = Auth::user();

        if ($user->isDinkesAuditor()) {
            abort(403, 'Auditor Dinas Kesehatan hanya memiliki hak akses pantau (Read-Only).');
        }

        $medicines = Medicine::orderBy('name', 'asc')->get();
        $facilities = Facility::where('type', '!=', 'DINKES')->get();

        return view('batches.create', [
            'medicines' => $medicines,
            'facilities' => $facilities,
            'user' => $user,
        ]);
    }

    /**
     * Store newly input batch into inventory ledger.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isDinkesAuditor()) {
            abort(403, 'Auditor Dinas Kesehatan hanya memiliki hak akses pantau.');
        }

        $facilityId = $user->facility_id;
        if ($user->role === 'SYSADMIN' && $request->filled('facility_id')) {
            $facilityId = (int) $request->get('facility_id');
        }

        if (! $facilityId) {
            return back()->withErrors(['facility_id' => 'Akun pengguna belum terasosiasi dengan fasilitas kesehatan.'])->withInput();
        }

        $isNewMedicine = $request->boolean('is_new_medicine');

        if ($isNewMedicine) {
            $validatedMed = $request->validate([
                'new_medicine_name' => 'required|string|max:150',
                'new_generic_name' => 'required|string|max:150',
                'new_category' => 'required|string|max:100',
                'new_dosage_form' => 'required|string|max:100',
                'new_unit' => 'required|string|max:50',
                'new_unit_price_idr' => 'required|numeric|min:0',
                'new_requires_cold_chain' => 'nullable|boolean',
                'new_storage_min_temp' => 'nullable|numeric',
                'new_storage_max_temp' => 'nullable|numeric',
                'new_handling_instructions' => 'nullable|string|max:500',
            ]);

            $medicineSku = 'MED-'.strtoupper(Str::random(6));

            $medicine = Medicine::create([
                'name' => $validatedMed['new_medicine_name'],
                'generic_name' => $validatedMed['new_generic_name'],
                'sku' => $medicineSku,
                'category' => $validatedMed['new_category'],
                'dosage_form' => $validatedMed['new_dosage_form'],
                'unit' => $validatedMed['new_unit'],
                'unit_price_idr' => $validatedMed['new_unit_price_idr'],
                'requires_cold_chain' => $request->boolean('new_requires_cold_chain'),
                'storage_min_temp' => $validatedMed['new_storage_min_temp'] ?? null,
                'storage_max_temp' => $validatedMed['new_storage_max_temp'] ?? null,
                'handling_instructions' => $validatedMed['new_handling_instructions'] ?? null,
            ]);

            $medicineId = $medicine->id;
        } else {
            $validatedMed = $request->validate([
                'medicine_id' => 'required|exists:medicines,id',
            ]);
            $medicineId = (int) $validatedMed['medicine_id'];
            $medicine = Medicine::findOrFail($medicineId);
        }

        $validatedBatch = $request->validate([
            'batch_number' => 'required|string|max:60',
            'current_stock' => 'required|integer|min:1',
            'daily_burn_rate' => 'required|numeric|min:0',
            'expiry_date' => 'required|date',
            'unit_price_idr' => 'nullable|numeric|min:0',
            'storage_location' => 'nullable|string|max:150',
        ]);

        $exists = Batch::where('facility_id', $facilityId)
            ->where('medicine_id', $medicineId)
            ->where('batch_number', $validatedBatch['batch_number'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'batch_number' => 'Nomor batch ini sudah terdaftar untuk obat tersebut di fasilitas Anda.',
            ])->withInput();
        }

        $unitPrice = $validatedBatch['unit_price_idr'] ?? $medicine->unit_price_idr;

        $batch = Batch::create([
            'facility_id' => $facilityId,
            'medicine_id' => $medicineId,
            'batch_number' => strtoupper(trim($validatedBatch['batch_number'])),
            'current_stock' => $validatedBatch['current_stock'],
            'daily_burn_rate' => $validatedBatch['daily_burn_rate'],
            'expiry_date' => $validatedBatch['expiry_date'],
            'unit_price_idr' => $unitPrice,
            'storage_location' => $validatedBatch['storage_location'] ?? 'Gudang Farmasi Utama',
            'status' => 'ACTIVE',
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $facilityId,
            'action' => 'BATCH_REGISTERED',
            'description' => "Pencatatan batch baru {$batch->batch_number} ({$medicine->name}) sejumlah {$batch->current_stock} {$medicine->unit} oleh {$user->name}.",
            'created_at' => now(),
        ]);

        return redirect()->route('batches.index')
            ->with('status', "Data stok batch {$batch->batch_number} ({$medicine->name}) berhasil disimpan ke sistem!");
    }

    /**
     * Show form to edit existing batch details.
     */
    public function edit(Batch $batch): View
    {
        $user = Auth::user();

        if (! $user->isDinkesAuditor() && $user->role !== 'SYSADMIN' && $user->facility_id !== $batch->facility_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit batch fasilitas lain.');
        }

        $batch->load(['medicine', 'facility']);

        return view('batches.edit', [
            'batch' => $batch,
            'user' => $user,
        ]);
    }

    /**
     * Update existing batch in inventory ledger.
     */
    public function update(Request $request, Batch $batch): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isDinkesAuditor() && $user->role !== 'SYSADMIN' && $user->facility_id !== $batch->facility_id) {
            abort(403, 'Anda tidak berwenang mengedit batch ini.');
        }

        $validated = $request->validate([
            'current_stock' => 'required|integer|min:0',
            'daily_burn_rate' => 'required|numeric|min:0',
            'expiry_date' => 'required|date',
            'unit_price_idr' => 'required|numeric|min:0',
            'storage_location' => 'nullable|string|max:150',
            'status' => 'required|in:ACTIVE,RESERVED,DEPLETED,EXPIRED',
        ]);

        $oldStock = $batch->current_stock;
        $batch->update($validated);

        $medicineName = $batch->medicine->name;

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $batch->facility_id,
            'action' => 'BATCH_UPDATED',
            'description' => "Pembaruan data batch {$batch->batch_number} ({$medicineName}). Stok diubah dari {$oldStock} menjadi {$batch->current_stock}.",
            'created_at' => now(),
        ]);

        return redirect()->route('batches.index')
            ->with('status', "Data batch {$batch->batch_number} berhasil diperbarui.");
    }

    /**
     * Remove batch from inventory ledger.
     */
    public function destroy(Batch $batch): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isDinkesAuditor() && $user->role !== 'SYSADMIN' && $user->facility_id !== $batch->facility_id) {
            abort(403, 'Anda tidak berwenang menghapus batch ini.');
        }

        // Prevent deletion if active transfers exist
        $hasActiveTransfers = $batch->transfers()
            ->whereIn('status', ['PROPOSED', 'APPROVED', 'ACCEPTED', 'IN_TRANSIT'])
            ->exists();

        if ($hasActiveTransfers) {
            return back()->withErrors([
                'delete_error' => 'Batch ini sedang terlibat dalam proses transfer aktif dan tidak dapat dihapus.',
            ]);
        }

        $batchNumber = $batch->batch_number;
        $medicineName = $batch->medicine->name;
        $facilityId = $batch->facility_id;

        $batch->delete();

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $facilityId,
            'action' => 'BATCH_DELETED',
            'description' => "Penghapusan data batch {$batchNumber} ({$medicineName}) oleh {$user->name}.",
            'created_at' => now(),
        ]);

        return redirect()->route('batches.index')
            ->with('status', "Batch {$batchNumber} berhasil dihapus dari inventaris.");
    }
}
