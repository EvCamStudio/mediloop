<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use App\Services\RedistributionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Display full-featured interactive geographical network map of healthcare facilities & logistics routes.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $userFacility = $user->facility;

        $facilitiesQuery = Facility::query();

        if ($request->filled('type')) {
            $facilitiesQuery->where('type', $request->get('type'));
        }

        if ($request->boolean('cold_chain_only')) {
            $facilitiesQuery->where('cold_chain_ready', true);
        }

        $facilities = $facilitiesQuery->get()->map(function ($f) use ($userFacility) {
            $activeBatches = Batch::where('facility_id', $f->id)->where('status', 'ACTIVE')->get();
            $atRiskCount = $activeBatches->filter(fn ($b) => in_array($b->risk_level, ['CRITICAL', 'HIGH']))->count();

            $distKm = null;
            if ($userFacility && $userFacility->id !== $f->id) {
                $distKm = RedistributionEngine::calculateHaversineDistance(
                    $userFacility->latitude,
                    $userFacility->longitude,
                    $f->latitude,
                    $f->longitude
                );
            }

            return [
                'id' => $f->id,
                'name' => $f->name,
                'code' => $f->code,
                'type' => $f->type,
                'address' => $f->address,
                'city' => $f->city,
                'latitude' => (float) $f->latitude,
                'longitude' => (float) $f->longitude,
                'cold_chain_ready' => (bool) $f->cold_chain_ready,
                'contact_person' => $f->contact_person,
                'phone' => $f->phone,
                'license_number' => $f->license_number,
                'active_batches_count' => $activeBatches->count(),
                'total_stock' => $activeBatches->sum('current_stock'),
                'at_risk_count' => $atRiskCount,
                'distance_from_user_km' => $distKm,
            ];
        });

        // Get transfer routes between facilities to render lines on map
        $transfers = Transfer::with(['donorFacility', 'recipientFacility', 'batch.medicine'])
            ->whereIn('status', ['PROPOSED', 'APPROVED', 'ACCEPTED', 'IN_TRANSIT', 'COMPLETED'])
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'transfer_code' => $t->transfer_code,
                    'status' => $t->status,
                    'medicine_name' => $t->batch->medicine->name ?? 'Obat',
                    'quantity' => $t->quantity,
                    'unit' => $t->batch->medicine->unit ?? 'Satuan',
                    'donor_id' => $t->donor_facility_id,
                    'donor_name' => $t->donorFacility->name,
                    'donor_lat' => (float) $t->donorFacility->latitude,
                    'donor_lng' => (float) $t->donorFacility->longitude,
                    'recipient_id' => $t->recipient_facility_id,
                    'recipient_name' => $t->recipientFacility->name,
                    'recipient_lat' => (float) $t->recipientFacility->latitude,
                    'recipient_lng' => (float) $t->recipientFacility->longitude,
                    'requires_cold_chain' => (bool) ($t->batch->medicine->requires_cold_chain ?? false),
                    'distance_km' => RedistributionEngine::calculateHaversineDistance(
                        $t->donorFacility->latitude,
                        $t->donorFacility->longitude,
                        $t->recipientFacility->latitude,
                        $t->recipientFacility->longitude
                    ),
                ];
            });

        // Summary counts
        $facilityStats = [
            'total' => Facility::count(),
            'rsud' => Facility::where('type', 'RSUD')->count(),
            'puskesmas' => Facility::where('type', 'PUSKESMAS')->count(),
            'klinik' => Facility::where('type', 'KLINIK')->count(),
            'cold_chain' => Facility::where('cold_chain_ready', true)->count(),
            'active_routes' => $transfers->whereIn('status', ['APPROVED', 'ACCEPTED', 'IN_TRANSIT'])->count(),
        ];

        return view('map.index', [
            'user' => $user,
            'userFacility' => $userFacility,
            'facilities' => $facilities,
            'transfers' => $transfers,
            'facilityStats' => $facilityStats,
            'currentType' => $request->get('type', ''),
            'coldChainOnly' => $request->boolean('cold_chain_only'),
        ]);
    }
}
