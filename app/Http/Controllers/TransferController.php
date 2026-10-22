<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use App\Services\RedistributionEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TransferController extends Controller
{
    /**
     * List all transfers relevant to the user's facility.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Transfer::with(['donorFacility', 'recipientFacility', 'batch.medicine']);

        if (! $user->isDinkesAuditor()) {
            $query->where(function ($q) use ($user) {
                $q->where('donor_facility_id', $user->facility_id)
                    ->orWhere('recipient_facility_id', $user->facility_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $transfers = $query->orderBy('updated_at', 'desc')->paginate(15);

        return view('transfers.index', [
            'transfers' => $transfers,
            'currentStatus' => $request->get('status'),
            'user' => $user,
        ]);
    }

    /**
     * Show candidate matching and redistribution proposal screen.
     */
    public function propose(Request $request, Batch $batch): View
    {
        $user = Auth::user();

        // Ensure user belongs to the donor facility or is authorized
        if (! $user->isDinkesAuditor() && $user->facility_id !== $batch->facility_id) {
            abort(403, 'Anda hanya dapat mengusulkan redistribusi untuk fasilitas Anda sendiri.');
        }

        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['score', 'distance'], true) ? $sort : 'score';

        $batch->load(['medicine', 'facility']);
        $candidates = RedistributionEngine::findCandidateRecipients($batch, $sort);

        return view('transfers.propose', [
            'batch' => $batch,
            'candidates' => $candidates,
            'sort' => $sort,
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created transfer proposal.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'recipient_facility_id' => 'required|exists:facilities,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $batch = Batch::with(['medicine', 'facility'])->findOrFail($validated['batch_id']);

        if ($validated['quantity'] > $batch->current_stock) {
            return back()->withErrors(['quantity' => 'Jumlah transfer melebihi stok yang tersedia.']);
        }

        $recipient = Facility::findOrFail($validated['recipient_facility_id']);

        // Distance & Scoring
        $distanceKm = RedistributionEngine::calculateHaversineDistance(
            $batch->facility->latitude,
            $batch->facility->longitude,
            $recipient->latitude,
            $recipient->longitude
        );

        $proximityScore = RedistributionEngine::calculateProximityScore($distanceKm);

        // Recipient stock & need calculation
        $existingBatch = Batch::where('facility_id', $recipient->id)
            ->where('medicine_id', $batch->medicine_id)
            ->where('status', 'ACTIVE')
            ->first();

        $recipientBurnRate = $existingBatch ? (float) $existingBatch->daily_burn_rate : 5.0;
        $recipientStock = $existingBatch ? $existingBatch->current_stock : 0;
        $daysLeft = RedistributionEngine::calculateDaysToDeplete($recipientStock, $recipientBurnRate);
        $needScore = $daysLeft <= 7 ? 95.0 : ($daysLeft <= 14 ? 80.0 : 60.0);
        $logisticsScore = $recipient->cold_chain_ready ? 90.0 : 70.0;
        $matchScore = RedistributionEngine::calculateMatchingScore($needScore, $proximityScore, $logisticsScore);

        $intakeCap = RedistributionEngine::calculateRecipientIntakeCap(
            $batch->projected_waste,
            $recipientBurnRate,
            $batch->days_to_expiry
        );

        $transferCode = 'TRF-'.date('Ymd').'-'.strtoupper(Str::random(4));

        $transfer = Transfer::create([
            'transfer_code' => $transferCode,
            'donor_facility_id' => $batch->facility_id,
            'recipient_facility_id' => $recipient->id,
            'batch_id' => $batch->id,
            'quantity' => $validated['quantity'],
            'status' => 'PROPOSED',
            'need_score' => $needScore,
            'proximity_score' => $proximityScore,
            'logistics_score' => $logisticsScore,
            'match_score' => $matchScore,
            'distance_km' => $distanceKm,
            'recipient_daily_burn_rate' => $recipientBurnRate,
            'recipient_intake_cap' => $intakeCap,
            'notes' => $validated['notes'],
            'proposed_by' => $user->id,
            'proposed_at' => now(),
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $batch->facility_id,
            'transfer_id' => $transfer->id,
            'action' => 'TRANSFER_PROPOSED',
            'description' => "Pengusulan transfer {$transferCode} oleh {$user->name} untuk {$validated['quantity']} unit ke {$recipient->name}.",
            'created_at' => now(),
        ]);

        return redirect()->route('transfers.show', $transfer)
            ->with('status', "Usulan transfer {$transferCode} berhasil diajukan dan menunggu persetujuan Kepala Instalasi Farmasi.");
    }

    /**
     * Show detailed transfer tracking and state timeline.
     */
    public function show(Transfer $transfer): View
    {
        $transfer->load([
            'donorFacility',
            'recipientFacility',
            'batch.medicine',
            'proposedByUser',
            'approvedByUser',
            'acceptedByUser',
            'completedByUser',
            'auditLogs.actor',
        ]);

        return view('transfers.show', [
            'transfer' => $transfer,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Approve transfer by Head of Pharmacy (Donor Approver).
     */
    public function approve(Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->canApproveDonorTransfer() && $user->facility_id !== $transfer->donor_facility_id) {
            abort(403, 'Hanya Kepala Instalasi Farmasi faskes donor yang dapat menyetujui pengeluaran obat.');
        }

        $transfer->update([
            'status' => 'APPROVED',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $transfer->donor_facility_id,
            'transfer_id' => $transfer->id,
            'action' => 'TRANSFER_APPROVED',
            'description' => "Persetujuan formal pengeluaran obat disahkan oleh {$user->name} ({$user->job_title}).",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Usulan transfer telah disetujui donor. Menunggu konfirmasi kesediaan penerima.');
    }

    /**
     * Accept transfer by Recipient Pharmacist / Director.
     */
    public function accept(Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isDinkesAuditor() && $user->facility_id !== $transfer->recipient_facility_id) {
            abort(403, 'Hanya perwakilan faskes penerima yang dapat menerima transfer ini.');
        }

        $transfer->update([
            'status' => 'ACCEPTED',
            'accepted_by' => $user->id,
            'accepted_at' => now(),
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $transfer->recipient_facility_id,
            'transfer_id' => $transfer->id,
            'action' => 'TRANSFER_ACCEPTED',
            'description' => "Faskes penerima ({$transfer->recipientFacility->name}) menyetujui penerimaan alokasi obat.",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Transfer diterima oleh faskes tujuan. Faskes donor dapat segera memproses pengiriman.');
    }

    /**
     * Dispatch shipment by Donor.
     */
    public function dispatchShipment(Request $request, Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'tracking_number' => 'required|string|max:100',
            'courier_type' => 'required|string|max:100',
        ]);

        $transfer->update([
            'status' => 'IN_TRANSIT',
            'tracking_number' => $validated['tracking_number'],
            'courier_type' => $validated['courier_type'],
            'shipped_at' => now(),
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $transfer->donor_facility_id,
            'transfer_id' => $transfer->id,
            'action' => 'IN_TRANSIT',
            'description' => "Obat diberangkatkan menggunakan {$validated['courier_type']} dengan nomor resi/kontrol: {$validated['tracking_number']}.",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Status pengiriman diperbarui menjadi Dalam Perjalanan (In Transit).');
    }

    /**
     * Confirm physical arrival and execute atomic stock mutation.
     */
    public function complete(Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->canConfirmPhysicalArrival() && $user->facility_id !== $transfer->recipient_facility_id) {
            abort(403, 'Hanya Kepala/Direktur faskes penerima yang berwenang memvalidasi penerimaan fisik obat.');
        }

        RedistributionEngine::completeTransfer($transfer, $user);

        return back()->with('status', 'Penerimaan fisik berhasil diverifikasi! Stok obat telah dimutasi secara atomik.');
    }

    /**
     * Cancel transfer.
     */
    public function cancel(Request $request, Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();
        $reason = $request->input('reason', 'Dibatalkan oleh pengguna.');

        $transfer->update([
            'status' => 'CANCELLED',
            'rejection_reason' => $reason,
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $user->facility_id,
            'transfer_id' => $transfer->id,
            'action' => 'TRANSFER_CANCELLED',
            'description' => "Transfer {$transfer->transfer_code} dibatalkan. Alasan: {$reason}",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Transfer berhasil dibatalkan.');
    }
}
