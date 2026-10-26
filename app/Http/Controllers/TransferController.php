<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use App\Models\User;
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

        $availableBatches = collect();
        if (! $user->isDinkesAuditor() && $user->facility_id) {
            $availableBatches = Batch::with('medicine')
                ->where('facility_id', $user->facility_id)
                ->where('status', 'ACTIVE')
                ->where('current_stock', '>', 0)
                ->orderBy('expiry_date', 'asc')
                ->get();
        }

        return view('transfers.index', [
            'transfers' => $transfers,
            'currentStatus' => $request->get('status'),
            'user' => $user,
            'availableBatches' => $availableBatches,
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
            abort(403, __('transfers.messages.propose_forbidden'));
        }

        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['score', 'distance'], true) ? $sort : 'score';
        $selectedRecipientId = $request->filled('recipient_id') ? $request->integer('recipient_id') : null;

        $batch->load(['medicine', 'facility']);
        $candidates = RedistributionEngine::findCandidateRecipients($batch, $sort);

        return view('transfers.propose', [
            'batch' => $batch,
            'candidates' => $candidates,
            'sort' => $sort,
            'user' => $user,
            'selectedRecipientId' => $selectedRecipientId,
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

        $batch = Batch::query()
            ->with(['medicine', 'facility'])
            ->whereKey($validated['batch_id'])
            ->firstOrFail();

        if (! $user instanceof User || (! $user->isSysadmin() && $user->facility_id !== $batch->facility_id)) {
            abort(403, __('transfers.messages.propose_forbidden'));
        }

        if ($batch->status !== 'ACTIVE' || $batch->facility === null) {
            return back()->withErrors(['batch_id' => __('transfers.messages.batch_unavailable')]);
        }

        if ($validated['quantity'] > $batch->current_stock) {
            return back()->withErrors(['quantity' => __('transfers.messages.quantity_exceeds_stock')]);
        }

        $recipient = Facility::query()
            ->whereKey($validated['recipient_facility_id'])
            ->firstOrFail();

        if ($recipient->is($batch->facility) || ! $recipient->isActive() || $recipient->type === 'DINKES') {
            return back()->withErrors(['recipient_facility_id' => __('transfers.messages.recipient_invalid')]);
        }

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
            'description' => __('transfers.messages.audit_proposed', [
                'code' => $transferCode,
                'name' => $user->name,
                'quantity' => $validated['quantity'],
                'recipient' => $recipient->name,
            ]),
            'created_at' => now(),
        ]);

        return redirect()->route('transfers.show', $transfer)
            ->with('status', __('transfers.messages.proposed_flash', ['code' => $transferCode]));
    }

    /**
     * Show detailed transfer tracking and state timeline.
     */
    public function show(Transfer $transfer): View
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        $isParty = in_array($user->facility_id, [
            $transfer->donor_facility_id,
            $transfer->recipient_facility_id,
        ], true);

        if (! $user->isDinkesAuditor() && ! $isParty) {
            abort(403, __('transfers.messages.access_denied'));
        }

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

        if (! $user instanceof User) {
            abort(403);
        }

        if ($transfer->status !== 'PROPOSED') {
            return back()->withErrors(['status' => __('transfers.messages.not_proposed_stage')]);
        }

        $isDonor = $user->isSysadmin() || $user->facility_id === $transfer->donor_facility_id;

        if (! $isDonor || ! $user->canApproveDonorTransfer()) {
            abort(403, __('transfers.messages.approve_forbidden'));
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
            'description' => __('transfers.messages.audit_approved', [
                'name' => $user->name,
                'job_title' => $user->job_title,
            ]),
            'created_at' => now(),
        ]);

        return back()->with('status', __('transfers.messages.approved_flash'));
    }

    /**
     * Accept transfer by Recipient Pharmacist / Director.
     */
    public function accept(Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        if ($transfer->status !== 'APPROVED') {
            return back()->withErrors(['status' => __('transfers.messages.not_approved_stage')]);
        }

        $isRecipient = $user->isSysadmin() || $user->facility_id === $transfer->recipient_facility_id;

        if (! $isRecipient || ! $user->canAcceptRecipientTransfer()) {
            abort(403, __('transfers.messages.accept_forbidden'));
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
            'description' => __('transfers.messages.audit_accepted', [
                'facility' => $transfer->recipientFacility->name,
            ]),
            'created_at' => now(),
        ]);

        return back()->with('status', __('transfers.messages.accepted_flash'));
    }

    /**
     * Dispatch shipment by Donor.
     */
    public function dispatchShipment(Request $request, Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        if ($transfer->status !== 'ACCEPTED') {
            return back()->withErrors(['status' => __('transfers.messages.not_accepted_stage')]);
        }

        $isDonor = $user->isSysadmin() || $user->facility_id === $transfer->donor_facility_id;

        if (! $isDonor || (! $user->isSysadmin() && ! $user->hasFacilityRole())) {
            abort(403, __('transfers.messages.dispatch_forbidden'));
        }

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
            'description' => __('transfers.messages.audit_dispatched', [
                'courier' => $validated['courier_type'],
                'tracking' => $validated['tracking_number'],
            ]),
            'created_at' => now(),
        ]);

        return back()->with('status', __('transfers.messages.in_transit_flash'));
    }

    /**
     * Confirm physical arrival and execute atomic stock mutation.
     */
    public function complete(Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        if ($transfer->status !== 'IN_TRANSIT') {
            return back()->withErrors(['status' => __('transfers.messages.not_in_transit_stage')]);
        }

        $isRecipient = $user->isSysadmin() || $user->facility_id === $transfer->recipient_facility_id;

        if (! $isRecipient || ! $user->canConfirmPhysicalArrival()) {
            abort(403, __('transfers.messages.complete_forbidden'));
        }

        RedistributionEngine::completeTransfer($transfer, $user);

        return back()->with('status', __('transfers.messages.completed_flash'));
    }

    /**
     * Cancel transfer.
     */
    public function cancel(Request $request, Transfer $transfer): RedirectResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        if (! in_array($transfer->status, ['PROPOSED', 'APPROVED', 'ACCEPTED'], true)) {
            return back()->withErrors(['status' => __('transfers.messages.cancel_not_allowed')]);
        }

        $isParty = $user->isSysadmin() || in_array($user->facility_id, [
            $transfer->donor_facility_id,
            $transfer->recipient_facility_id,
        ], true);

        if (! $isParty) {
            abort(403, __('transfers.messages.cancel_forbidden'));
        }

        $reason = $request->input('reason', __('transfers.messages.cancel_reason_default'));

        $transfer->update([
            'status' => 'CANCELLED',
            'rejection_reason' => $reason,
        ]);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $user->facility_id,
            'transfer_id' => $transfer->id,
            'action' => 'TRANSFER_CANCELLED',
            'description' => __('transfers.messages.audit_cancelled', [
                'code' => $transfer->transfer_code,
                'reason' => $reason,
            ]),
            'created_at' => now(),
        ]);

        return back()->with('status', __('transfers.messages.cancelled_flash'));
    }
}
