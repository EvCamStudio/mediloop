<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Services\RedistributionEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BatchController extends Controller
{
    /**
     * Display inventory batch list with search, filter, and risk levels.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $facilityId = $user->facility_id;

        if ($user->isDinkesAuditor() && $request->filled('facility_id')) {
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
        $totalWasteUnits = (int) $statsBatches->sum('projected_waste');
        $estimatedWasteKg = round($totalWasteUnits * 0.085, 1);

        $topRiskBatch = $statsBatches->first(fn ($b) => in_array($b->risk_level, ['CRITICAL', 'HIGH']) && $b->projected_waste > 0 && $b->current_stock > 0);
        $topCandidates = [];
        if ($topRiskBatch) {
            $topCandidates = array_slice(RedistributionEngine::findCandidateRecipients($topRiskBatch), 0, 3);
        }

        $batches = $query->orderBy('expiry_date', 'asc')->paginate(15)->withQueryString();

        $facilitiesList = Facility::where('type', '!=', 'DINKES')->where('status', 'ACTIVE')->get();

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
            'totalWasteUnits' => $totalWasteUnits,
            'estimatedWasteKg' => $estimatedWasteKg,
            'topRiskBatch' => $topRiskBatch,
            'topCandidates' => $topCandidates,
        ]);
    }

    /**
     * Show form to input a new pharmaceutical batch.
     */
    public function create(): View
    {
        $user = Auth::user();

        if ($user->isDinkesAuditor() && ! $user->isSysadmin()) {
            abort(403, __('batches.errors.auditor_read_only'));
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

        if ($user->isDinkesAuditor() && ! $user->isSysadmin()) {
            abort(403, __('batches.errors.auditor_read_only_short'));
        }

        $facilityId = $user->facility_id;
        if ($user->isSysadmin() && $request->filled('facility_id')) {
            $facilityId = (int) $request->get('facility_id');
        }

        if (! $facilityId) {
            return back()->withErrors(['facility_id' => __('batches.errors.no_facility')])->withInput();
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
                'batch_number' => __('batches.errors.batch_duplicate'),
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
            'storage_location' => $validatedBatch['storage_location'] ?? __('batches.default_storage_location'),
            'status' => 'ACTIVE',
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $facilityId,
            'action' => 'BATCH_REGISTERED',
            'description' => __('batches.audit.registered', [
                'batch' => $batch->batch_number,
                'medicine' => $medicine->name,
                'stock' => $batch->current_stock,
                'unit' => $medicine->unit,
                'user' => $user->name,
            ]),
            'created_at' => now(),
        ]);

        return redirect()->route('batches.index')
            ->with('status', __('batches.flash.stored', ['batch' => $batch->batch_number, 'medicine' => $medicine->name]));
    }

    /**
     * Show form to edit existing batch details.
     */
    public function edit(Batch $batch): View
    {
        $user = Auth::user();

        if (! $user->isDinkesAuditor() && $user->facility_id !== $batch->facility_id) {
            abort(403, __('batches.errors.not_allowed_edit_other'));
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

        if (! $user->isDinkesAuditor() && $user->facility_id !== $batch->facility_id) {
            abort(403, __('batches.errors.not_allowed_update'));
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
            'description' => __('batches.audit.updated', [
                'batch' => $batch->batch_number,
                'medicine' => $medicineName,
                'old' => $oldStock,
                'new' => $batch->current_stock,
            ]),
            'created_at' => now(),
        ]);

        return redirect()->route('batches.index')
            ->with('status', __('batches.flash.updated', ['batch' => $batch->batch_number]));
    }

    /**
     * Remove batch from inventory ledger.
     */
    public function destroy(Batch $batch): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isDinkesAuditor() && $user->facility_id !== $batch->facility_id) {
            abort(403, __('batches.errors.not_allowed_delete'));
        }

        // Prevent deletion if active transfers exist
        $hasActiveTransfers = $batch->transfers()
            ->whereIn('status', ['PROPOSED', 'APPROVED', 'ACCEPTED', 'IN_TRANSIT'])
            ->exists();

        if ($hasActiveTransfers) {
            return back()->withErrors([
                'delete_error' => __('batches.errors.active_transfer'),
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
            'description' => __('batches.audit.deleted', [
                'batch' => $batchNumber,
                'medicine' => $medicineName,
                'user' => $user->name,
            ]),
            'created_at' => now(),
        ]);

        return redirect()->route('batches.index')
            ->with('status', __('batches.flash.deleted', ['batch' => $batchNumber]));
    }

    /**
     * Download standardized CSV template for bulk medicine batch import.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_stok_obat_mediloop.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            if (! is_resource($handle)) {
                return;
            }

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, [
                'nama_obat',
                'nama_generik',
                'kategori',
                'bentuk_sediaan',
                'satuan',
                'nomor_batch',
                'stok_saat_ini',
                'laju_pemakaian_harian',
                'tanggal_kadaluwarsa',
                'harga_satuan_idr',
                'perlu_rantai_dingin',
                'lokasi_simpan',
            ]);

            // Sample realistic rows
            fputcsv($handle, [
                'Amoxicillin Trihydrate 500mg',
                'Amoxicillin',
                'Antibiotik',
                'Kapsul',
                'Strip (10 Kapsul)',
                'AMX-2026-B1',
                '450',
                '5.5',
                date('Y-m-d', strtotime('+45 days')),
                '12500',
                'TIDAK',
                'Gudang Farmasi A-1',
            ]);

            fputcsv($handle, [
                'Insulin Glargine 100 IU/mL',
                'Insulin Glargine',
                'Antidiabetes / Hormon',
                'Pen Injector 3mL',
                'Cartridge',
                'INS-2026-X8',
                '120',
                '2.0',
                date('Y-m-d', strtotime('+35 days')),
                '185000',
                'YA',
                'Kulkas Farmasi Cold Chain #2 (2-8°C)',
            ]);

            fputcsv($handle, [
                'Paracetamol Sirup 120mg/5mL',
                'Paracetamol',
                'Analgesik & Antipiretik',
                'Sirup 60mL',
                'Botol',
                'PCT-2026-S3',
                '300',
                '8.0',
                date('Y-m-d', strtotime('+90 days')),
                '8500',
                'TIDAK',
                'Gudang Farmasi Utama B-3',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Parse and sanitize date string into Y-m-d format.
     */
    private function parseExpiryDate(mixed $rawDate): ?string
    {
        if (empty($rawDate)) {
            return null;
        }

        $rawDate = trim((string) $rawDate);

        // Format: YYYY-MM-DD
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $rawDate, $m)) {
            if (checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
                return sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]);
            }
        }

        // Format: DD/MM/YYYY
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $rawDate, $m)) {
            if (checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
                return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
            }
        }

        // Format: DD-MM-YYYY
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $rawDate, $m)) {
            if (checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
                return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
            }
        }

        // Excel numeric serial (e.g. 45000 to 55000)
        if (is_numeric($rawDate) && (int) $rawDate > 30000 && (int) $rawDate < 80000) {
            $unixTime = ((int) $rawDate - 25569) * 86400;

            return gmdate('Y-m-d', $unixTime);
        }

        // General DateTime fallback
        try {
            $dt = new \DateTime($rawDate);

            return $dt->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Parse physical stock into a positive integer.
     */
    private function parseIntegerStock(mixed $val): int
    {
        if (is_int($val)) {
            return max(0, $val);
        }
        if (is_float($val)) {
            return max(0, (int) round($val));
        }
        if (! is_string($val)) {
            return 0;
        }

        $clean = trim(preg_replace('/[^0-9,.\-]/', '', $val));

        // Format: exact thousands like 1.000 or 1,000 or 25.000
        if (preg_match('/^\d{1,3}[,.]\d{3}$/', $clean)) {
            return (int) preg_replace('/[,.]/', '', $clean);
        }

        // Format: decimal zero like 450.0 or 450.00 or 450,0
        if (preg_match('/^(\d+)[,.]0{1,2}$/', $clean, $m)) {
            return (int) $m[1];
        }

        $digits = preg_replace('/[^0-9\-]/', '', $clean);

        return is_numeric($digits) ? max(0, (int) $digits) : 0;
    }

    /**
     * Parse daily burn rate into a valid positive float.
     */
    private function parseDailyBurnRate(mixed $val): float
    {
        if (is_float($val) || is_int($val)) {
            return max(0.1, (float) $val);
        }
        if (! is_string($val)) {
            return 1.0;
        }

        $clean = trim(preg_replace('/[^0-9,.\-]/', '', $val));

        if (str_contains($clean, ',') && ! str_contains($clean, '.')) {
            $clean = str_replace(',', '.', $clean);
        }

        $num = is_numeric($clean) ? (float) $clean : 1.0;

        return $num > 0 ? round($num, 2) : 1.0;
    }

    /**
     * Handle bulk pharmaceutical stock import from user CSV file.
     */
    public function importCsv(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isDinkesAuditor()) {
            abort(403, 'Auditor Dinas Kesehatan hanya memiliki hak akses pantau (Read-Only).');
        }

        $request->validate([
            'csv_file' => 'required|file|max:5120',
        ], [
            'csv_file.required' => 'Silakan pilih berkas CSV terlebih dahulu dari komputer Anda.',
            'csv_file.file' => 'Berkas yang diunggah tidak valid.',
            'csv_file.max' => 'Ukuran berkas CSV maksimal 5 MB.',
        ]);

        $facilityId = $user->facility_id;
        if ($user->role === 'SYSADMIN' && $request->filled('facility_id')) {
            $facilityId = (int) $request->get('facility_id');
        }

        if (! $facilityId) {
            return back()->withErrors(['csv_file' => 'Akun pengguna belum terasosiasi dengan fasilitas kesehatan.']);
        }

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Read first line to detect separator
            $firstLine = fgets($handle) ?: '';
            rewind($handle);

            // Strip UTF-8 BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            // Determine delimiter: comma, semicolon, or tab
            $commaCount = substr_count($firstLine, ',');
            $semicolonCount = substr_count($firstLine, ';');
            $tabCount = substr_count($firstLine, "\t");

            $delimiter = ',';
            if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
                $delimiter = ';';
            } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
                $delimiter = "\t";
            }

            while (($data = fgetcsv($handle, 4096, $delimiter)) !== false) {
                $rows[] = array_map(fn ($val) => is_string($val) ? trim($val) : '', $data);
            }
            fclose($handle);
        }

        if (count($rows) < 2) {
            return back()->withErrors(['csv_file' => 'Berkas CSV kosong atau tidak memiliki baris data obat.']);
        }

        // Standardize header row keys
        $rawHeaders = array_map(function ($h) {
            return strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-', '/'], '_', $h)));
        }, $rows[0]);

        $headerMap = [];
        foreach ($rawHeaders as $idx => $h) {
            if (in_array($h, ['nama_obat', 'medicine_name', 'nama', 'name', 'obat', 'item_name'])) {
                $headerMap['medicine_name'] = $idx;
            }
            if (in_array($h, ['nama_generik', 'generic_name', 'generik', 'zat_aktif'])) {
                $headerMap['generic_name'] = $idx;
            }
            if (in_array($h, ['kategori', 'category', 'golongan', 'kelompok'])) {
                $headerMap['category'] = $idx;
            }
            if (in_array($h, ['bentuk_sediaan', 'dosage_form', 'sediaan', 'form'])) {
                $headerMap['dosage_form'] = $idx;
            }
            if (in_array($h, ['satuan', 'unit', 'kemasan', 'kemasan_satuan'])) {
                $headerMap['unit'] = $idx;
            }
            if (in_array($h, ['nomor_batch', 'batch_number', 'batch_no', 'no_batch', 'batch', 'lot', 'no_lot'])) {
                $headerMap['batch_number'] = $idx;
            }
            if (in_array($h, ['stok_saat_ini', 'current_stock', 'stok', 'stock', 'jumlah', 'qty', 'kuantitas', 'sisa_stok'])) {
                $headerMap['current_stock'] = $idx;
            }
            if (in_array($h, ['laju_pemakaian_harian', 'daily_burn_rate', 'burn_rate', 'pemakaian_harian', 'laju_harian', 'burn'])) {
                $headerMap['daily_burn_rate'] = $idx;
            }
            if (in_array($h, ['tanggal_kadaluwarsa', 'expiry_date', 'kadaluwarsa', 'expired_date', 'exp_date', 'expired', 'ed', 'tgl_ed'])) {
                $headerMap['expiry_date'] = $idx;
            }
            if (in_array($h, ['harga_satuan_idr', 'harga_satuan', 'unit_price_idr', 'harga', 'price', 'tarif'])) {
                $headerMap['unit_price_idr'] = $idx;
            }
            if (in_array($h, ['perlu_rantai_dingin', 'requires_cold_chain', 'cold_chain', 'rantai_dingin', 'cold'])) {
                $headerMap['requires_cold_chain'] = $idx;
            }
            if (in_array($h, ['lokasi_simpan', 'storage_location', 'lokasi', 'rak', 'gudang'])) {
                $headerMap['storage_location'] = $idx;
            }
        }

        if (! isset($headerMap['medicine_name']) || ! isset($headerMap['batch_number']) || ! isset($headerMap['current_stock']) || ! isset($headerMap['expiry_date'])) {
            return back()->withErrors([
                'csv_file' => 'Format kolom berkas CSV belum sesuai. Kolom wajib yang harus ada: nama_obat, nomor_batch, stok_saat_ini, tanggal_kadaluwarsa. Silakan unduh template CSV resmi MediLoop.',
            ]);
        }

        $importedCount = 0;
        $updatedCount = 0;
        $failedCount = 0;
        $skippedRows = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty(array_filter($row))) {
                continue;
            }

            $medName = trim($row[$headerMap['medicine_name']] ?? '');
            $batchNum = strtoupper(trim($row[$headerMap['batch_number']] ?? ''));
            $rawStock = $row[$headerMap['current_stock']] ?? '';
            $stock = $this->parseIntegerStock($rawStock);
            $rawExp = $row[$headerMap['expiry_date']] ?? '';
            $expiryDate = $this->parseExpiryDate($rawExp);

            if (empty($medName) || empty($batchNum) || $stock <= 0 || ! $expiryDate) {
                $failedCount++;
                $skippedRows[] = "Baris #{$i}: Nama obat '{$medName}', Batch '{$batchNum}', Stok '{$rawStock}', Exp '{$rawExp}' tidak valid.";

                continue;
            }

            $burnRate = isset($headerMap['daily_burn_rate'])
                ? $this->parseDailyBurnRate($row[$headerMap['daily_burn_rate']] ?? 1.0)
                : 1.0;

            $rawPrice = isset($headerMap['unit_price_idr']) ? $row[$headerMap['unit_price_idr']] : null;
            $unitPrice = $rawPrice ? $this->parseDailyBurnRate($rawPrice) : 0;

            $genericName = isset($headerMap['generic_name']) && ! empty($row[$headerMap['generic_name']])
                ? trim($row[$headerMap['generic_name']])
                : $medName;

            $category = isset($headerMap['category']) && ! empty($row[$headerMap['category']])
                ? trim($row[$headerMap['category']])
                : 'Umum / Generik';

            $dosageForm = isset($headerMap['dosage_form']) && ! empty($row[$headerMap['dosage_form']])
                ? trim($row[$headerMap['dosage_form']])
                : 'Tablet';

            $unit = isset($headerMap['unit']) && ! empty($row[$headerMap['unit']])
                ? trim($row[$headerMap['unit']])
                : 'Satuan';

            $storageLoc = isset($headerMap['storage_location']) && ! empty($row[$headerMap['storage_location']])
                ? trim($row[$headerMap['storage_location']])
                : 'Gudang Farmasi Utama';

            $coldChainRaw = isset($headerMap['requires_cold_chain'])
                ? strtolower(trim((string) $row[$headerMap['requires_cold_chain']]))
                : 'tidak';
            $requiresColdChain = in_array($coldChainRaw, ['ya', 'yes', 'true', '1', 'dingin', 'cold']);

            // 1. SMART RECONCILIATION: Find or create Medicine
            $cleanLower = strtolower($medName);
            $medicine = Medicine::whereRaw('LOWER(TRIM(name)) = ?', [$cleanLower])
                ->orWhereRaw('LOWER(TRIM(generic_name)) = ?', [$cleanLower])
                ->first();

            if (! $medicine && ! empty($genericName)) {
                $cleanGeneric = strtolower($genericName);
                $medicine = Medicine::whereRaw('LOWER(TRIM(name)) = ?', [$cleanGeneric])
                    ->orWhereRaw('LOWER(TRIM(generic_name)) = ?', [$cleanGeneric])
                    ->first();
            }

            if (! $medicine) {
                $medicine = Medicine::create([
                    'name' => $medName,
                    'generic_name' => $genericName ?: $medName,
                    'sku' => 'MED-'.strtoupper(Str::random(6)),
                    'category' => $category,
                    'dosage_form' => $dosageForm,
                    'unit' => $unit,
                    'unit_price_idr' => $unitPrice > 0 ? $unitPrice : 10000,
                    'requires_cold_chain' => $requiresColdChain,
                    'storage_min_temp' => $requiresColdChain ? 2.0 : 15.0,
                    'storage_max_temp' => $requiresColdChain ? 8.0 : 30.0,
                    'handling_instructions' => $requiresColdChain
                        ? 'Simpan pada suhu 2-8°C (Rantai Dingin / Cold Chain)'
                        : 'Simpan pada suhu sejuk 15-25°C',
                ]);
            }

            if ($unitPrice <= 0) {
                $unitPrice = (float) $medicine->unit_price_idr;
            }

            // 2. SMART RECONCILIATION: Match existing batch or create new batch
            $existingBatch = Batch::where('facility_id', $facilityId)
                ->where('medicine_id', $medicine->id)
                ->where('batch_number', $batchNum)
                ->first();

            if ($existingBatch) {
                $existingBatch->update([
                    'current_stock' => $stock,
                    'daily_burn_rate' => $burnRate,
                    'expiry_date' => $expiryDate,
                    'unit_price_idr' => $unitPrice,
                    'storage_location' => $storageLoc ?: $existingBatch->storage_location,
                    'status' => 'ACTIVE',
                ]);
                $updatedCount++;
            } else {
                Batch::create([
                    'facility_id' => $facilityId,
                    'medicine_id' => $medicine->id,
                    'batch_number' => $batchNum,
                    'current_stock' => $stock,
                    'daily_burn_rate' => $burnRate,
                    'expiry_date' => $expiryDate,
                    'unit_price_idr' => $unitPrice,
                    'storage_location' => $storageLoc,
                    'status' => 'ACTIVE',
                ]);
                $importedCount++;
            }
        }

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $facilityId,
            'action' => 'BATCH_CSV_IMPORTED',
            'description' => "Impor data stok batch via file CSV oleh {$user->name}: {$importedCount} batch baru ditambahkan, {$updatedCount} batch diperbarui, {$failedCount} baris diabaikan.",
            'created_at' => now(),
        ]);

        $msg = "Impor CSV berhasil diproses! {$importedCount} batch baru ditambahkan ke inventaris, {$updatedCount} batch diperbarui.";
        if ($failedCount > 0) {
            $msg .= " ({$failedCount} baris tidak lengkap dilewati).";
        }

        return redirect()->route('batches.index')->with('status', $msg);
    }
}
