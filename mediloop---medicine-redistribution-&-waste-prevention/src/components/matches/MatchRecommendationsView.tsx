import React, { useState } from 'react';
import { useApp } from '../../context/AppContext';
import { DrugBatch, MatchCandidate } from '../../types';
import { computeBatchForecast, computeMatchesForBatch } from '../../utils/calculations';
import { Sparkles, MapPin, ArrowRight, ShieldCheck, CheckCircle2, ChevronRight, AlertTriangle, Layers } from 'lucide-react';
import confetti from 'canvas-confetti';

interface MatchRecommendationsViewProps {
  initialBatchId?: string | null;
  onTransferCreated?: () => void;
  onOpenMap?: () => void;
}

export const MatchRecommendationsView: React.FC<MatchRecommendationsViewProps> = ({
  initialBatchId,
  onTransferCreated,
  onOpenMap,
}) => {
  const { facilities, batches, drugs, burnRates, currentFacility, scoreWeights, createTransfer } = useApp();

  // Find all batches belonging to current facility that are at risk
  const facilityBatches = batches.filter((b) => b.facility_id === currentFacility.id);

  const atRiskBatches = facilityBatches.map((batch) => {
    const burnRateKey = `${batch.facility_id}_${batch.drug_id}`;
    const burnRate = burnRates[burnRateKey] || 2.0;
    const forecast = computeBatchForecast(batch, burnRate, 30);
    const drug = drugs.find((d) => d.id === batch.drug_id);
    return { batch, forecast, drug };
  });

  // Default to initialBatchId, or first high-risk batch, or first batch
  const defaultSelectedId =
    initialBatchId ||
    atRiskBatches.find((item) => item.forecast.risk_level === 'HIGH')?.batch.id ||
    atRiskBatches[0]?.batch.id;

  const [selectedBatchId, setSelectedBatchId] = useState<string>(defaultSelectedId || '');
  const [transferQuantities, setTransferQuantities] = useState<Record<string, number>>({});
  const [createdTransferId, setCreatedTransferId] = useState<string | null>(null);

  const selectedItem = atRiskBatches.find((item) => item.batch.id === selectedBatchId);

  // Compute matches
  const matchCandidates: MatchCandidate[] = React.useMemo(() => {
    if (!selectedItem) return [];
    return computeMatchesForBatch(
      currentFacility,
      selectedItem.batch,
      selectedItem.forecast,
      facilities,
      batches,
      burnRates,
      scoreWeights
    );
  }, [selectedItem, currentFacility, facilities, batches, burnRates, scoreWeights]);

  const handleQtyChange = (facilityId: string, val: number) => {
    setTransferQuantities((prev) => ({ ...prev, [facilityId]: val }));
  };

  const handleInitiateTransfer = (candidate: MatchCandidate) => {
    if (!selectedItem) return;
    const qty = transferQuantities[candidate.candidate_facility.id] || candidate.recommended_transfer_qty;

    if (
      qty <= 0 ||
      qty > selectedItem.forecast.projected_waste ||
      qty > candidate.estimated_need
    ) {
      return;
    }

    const newTransfer = createTransfer(
      selectedItem.batch.id,
      candidate.candidate_facility.id,
      qty,
      `Optimized MediLoop redistribution (Match score ${candidate.score}%)`
    );

    setCreatedTransferId(newTransfer.id);
    confetti({
      particleCount: 60,
      spread: 60,
      origin: { y: 0.7 },
    });

    if (onTransferCreated) {
      setTimeout(() => {
        onTransferCreated();
      }, 1200);
    }
  };

  return (
    <div className="space-y-6 font-body text-slate-800">
      {/* Page Header */}
      <div className="bg-white p-6 rounded-2xl border border-[#DBE5F5] shadow-xs flex flex-wrap items-center justify-between gap-4">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF1FF] border border-[#DBE5F5] text-[#1D4ED8] text-xs font-bold mb-2">
            <Sparkles className="w-3.5 h-3.5 text-[#1D4ED8]" />
            <span>Intelligent Matching & Redistribution Engine</span>
          </div>
          <h2 className="text-xl font-bold text-[#0B1F4B]">
            Surplus Recipient Recommendations
          </h2>
          <p className="text-xs text-[#52627F] mt-1 max-w-2xl">
            The algorithmic engine evaluates daily burn rate velocity, shelf-life compatibility, and geographic proximity (Haversine)
            to recommend qualified recipient health facilities without risk of secondary overstock.
          </p>
        </div>

        {/* Weights Summary Badge */}
        <div className="bg-[#F8FAFC] border border-[#DBE5F5] p-3 rounded-xl text-xs space-y-1">
          <p className="font-bold text-[#0B1F4B]">Algorithm Scoring Weights:</p>
          <div className="flex items-center gap-3 text-xs text-[#52627F] font-medium">
            <span>Deficit: {Math.round(scoreWeights.shortage_weight * 100)}%</span>
            <span>•</span>
            <span>Distance: {Math.round(scoreWeights.distance_weight * 100)}%</span>
            <span>•</span>
            <span>Shelf Life: {Math.round(scoreWeights.shelf_life_weight * 100)}%</span>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left Column: Select At-Risk Batch */}
        <div className="bg-white p-5 rounded-2xl border border-[#DBE5F5] shadow-xs space-y-3">
          <h3 className="text-xs font-bold text-[#0B1F4B] uppercase tracking-wider flex items-center justify-between">
            <span>Select At-Risk Batch ({facilityBatches.length})</span>
            <span className="text-[#52627F] font-normal">Facility: {currentFacility.name.replace('Puskesmas ', '')}</span>
          </h3>

          <div className="space-y-2 max-h-[560px] overflow-y-auto pr-1">
            {atRiskBatches.map(({ batch, forecast, drug }) => {
              const isSelected = batch.id === selectedBatchId;
              let badgeColor = 'bg-[#EAF1FF] text-[#1D4ED8] border-[#DBE5F5]';
              if (forecast.risk_level === 'HIGH') {
                badgeColor = 'bg-[#D9480F] text-white border-transparent';
              } else if (forecast.risk_level === 'MEDIUM') {
                badgeColor = 'bg-[#B7791F] text-white border-transparent';
              } else if (forecast.risk_level === 'WATCH') {
                badgeColor = 'bg-[#EAF1FF] text-[#1D4ED8] border-[#DBE5F5]';
              }

              return (
                <button
                  key={batch.id}
                  onClick={() => {
                    setSelectedBatchId(batch.id);
                    setCreatedTransferId(null);
                  }}
                  className={`w-full text-left p-3.5 rounded-xl border transition-all cursor-pointer ${
                    isSelected
                      ? 'border-[#1D4ED8] bg-[#EAF1FF]/40 shadow-xs ring-2 ring-blue-500/20'
                      : 'border-[#DBE5F5] hover:border-[#1D4ED8]/40 hover:bg-[#F8FAFC]'
                  }`}
                >
                  <div className="flex items-center justify-between">
                    <span className="font-bold text-[#0B1F4B] text-xs">{drug?.name}</span>
                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded border ${badgeColor}`}>
                      {forecast.risk_level}
                    </span>
                  </div>
                  <div className="text-xs text-[#52627F] mt-1 flex items-center justify-between">
                    <span className="font-mono">Batch #{batch.batch_no}</span>
                    <span>Stock: <b>{batch.stock}</b> boxes</span>
                  </div>
                  <div className="mt-2 text-xs text-[#52627F] flex items-center justify-between border-t border-[#DBE5F5] pt-1.5">
                    <span>Expires: {forecast.days_to_expiry} days</span>
                    <span className="font-bold text-[#D9480F]">
                      Surplus: {forecast.projected_waste} boxes
                    </span>
                  </div>
                </button>
              );
            })}
          </div>
        </div>

        {/* Right Column: Ranked Candidate Receivers */}
        <div className="lg:col-span-2 space-y-4">
          {selectedItem && (
            <div className="bg-white p-5 rounded-2xl border border-[#DBE5F5] shadow-xs">
              <div className="flex flex-wrap items-center justify-between gap-4 border-b border-[#DBE5F5] pb-4">
                <div>
                  <span className="text-[10px] font-bold text-[#1D4ED8] bg-[#EAF1FF] border border-[#DBE5F5] px-2 py-0.5 rounded uppercase">
                    Target Batch Selected
                  </span>
                  <h3 className="text-base font-bold text-[#0B1F4B] mt-1">
                    {selectedItem.drug?.name} (Batch #{selectedItem.batch.batch_no})
                  </h3>
                  <p className="text-xs text-[#52627F]">
                    Expires on {selectedItem.batch.expiry_date} ({selectedItem.forecast.days_to_expiry} days remaining) •
                    Daily burn rate: {selectedItem.forecast.burn_rate} boxes/day
                  </p>
                </div>

                <div className="flex items-center gap-3">
                  <div className="bg-[#F8FAFC] border border-[#DBE5F5] rounded-xl px-3 py-2 text-center">
                    <p className="text-[10px] uppercase font-bold text-[#D9480F]">Projected Waste</p>
                    <p className="text-base font-extrabold text-[#D9480F]">
                      {selectedItem.forecast.projected_waste} Boxes
                    </p>
                  </div>
                  {onOpenMap && (
                    <button
                      onClick={onOpenMap}
                      className="px-3.5 py-2 text-xs font-semibold border border-[#DBE5F5] text-[#1D4ED8] bg-white hover:bg-[#EAF1FF] rounded-xl flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer"
                    >
                      <MapPin className="w-3.5 h-3.5" />
                      <span>View on Network Map</span>
                    </button>
                  )}
                </div>
              </div>

              {/* Candidates List */}
              <div className="mt-5 space-y-3">
                <div className="flex items-center justify-between">
                  <h4 className="text-xs font-bold text-[#0B1F4B] uppercase tracking-wider">
                    Qualified Recipient Facilities ({matchCandidates.length})
                  </h4>
                  <span className="text-xs text-[#52627F]">
                    Ranked by highest AI match score
                  </span>
                </div>

                {matchCandidates.length > 0 ? (
                  <div className="space-y-3">
                    {matchCandidates.map((candidate, idx) => {
                      const qty =
                        transferQuantities[candidate.candidate_facility.id] ??
                        candidate.recommended_transfer_qty;

                      return (
                        <div
                          key={candidate.candidate_facility.id}
                          className="p-4 rounded-xl border border-[#DBE5F5] hover:border-[#1D4ED8]/40 transition-all bg-[#F8FAFC]/50"
                        >
                          <div className="flex flex-wrap items-center justify-between gap-3">
                            <div className="flex items-center gap-3">
                              <div className="w-9 h-9 rounded-xl bg-[#1D4ED8] text-white font-extrabold text-sm flex items-center justify-center shrink-0">
                                #{idx + 1}
                              </div>
                              <div>
                                <div className="flex items-center gap-2">
                                  <h4 className="font-bold text-sm text-[#0B1F4B]">
                                    {candidate.candidate_facility.name}
                                  </h4>
                                  <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
                                    {candidate.candidate_facility.type}
                                  </span>
                                </div>
                                <p className="text-xs text-[#52627F] flex items-center gap-1 mt-0.5">
                                  <MapPin className="w-3 h-3 text-[#52627F]" />
                                  <span>{candidate.candidate_facility.address}</span>
                                  <span className="font-bold text-[#1D4ED8]">
                                    • {candidate.distance_km} km
                                  </span>
                                </p>
                              </div>
                            </div>

                            {/* Overall Match Score */}
                            <div className="flex items-center gap-3">
                              <div className="text-right">
                                <span className="text-[10px] text-[#52627F] font-medium">Match Score</span>
                                <p className="text-lg font-extrabold text-[#1D4ED8]">
                                  {candidate.score}%
                                </p>
                              </div>
                            </div>
                          </div>

                          {/* Reason and Metrics Breakdown */}
                          <div className="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs">
                            <div className="bg-white p-2.5 rounded-lg border border-[#DBE5F5]">
                              <span className="text-[10px] text-[#52627F] block">Stock Deficit Level</span>
                              <span className="font-bold text-[#0B1F4B]">
                                Remaining {candidate.receiver_current_stock} boxes (Burn {candidate.receiver_burn_rate}/day)
                              </span>
                            </div>
                            <div className="bg-white p-2.5 rounded-lg border border-[#DBE5F5]">
                              <span className="text-[10px] text-[#52627F] block">Estimated Need</span>
                              <span className="font-bold text-[#B7791F]">
                                {candidate.estimated_need} boxes before replenishment
                              </span>
                            </div>
                            <div className="bg-white p-2.5 rounded-lg border border-[#DBE5F5]">
                              <span className="text-[10px] text-[#52627F] block">Shelf Life Feasibility</span>
                              <span className="font-bold text-[#1D4ED8]">
                                Full depletion before expiry (&lt; {selectedItem.forecast.days_to_expiry} days)
                              </span>
                            </div>
                          </div>

                          <div className="mt-2.5 text-xs text-[#52627F] italic">
                            💡 <b>Algorithm Rationale:</b> {candidate.reason}
                          </div>

                          {/* Transfer Action Bar & Validation */}
                          {(() => {
                            const exceedsWaste = qty > selectedItem.forecast.projected_waste;
                            const exceedsNeed = qty > candidate.estimated_need;
                            const isInvalid = qty <= 0 || exceedsWaste || exceedsNeed;

                            return (
                              <div className="mt-3 pt-3 border-t border-[#DBE5F5] space-y-2">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                  <div className="flex items-center gap-2 text-xs">
                                    <span className="font-semibold text-[#0B1F4B]">Transfer Allocation:</span>
                                    <input
                                      type="number"
                                      min="1"
                                      value={qty}
                                      onChange={(e) =>
                                        handleQtyChange(
                                          candidate.candidate_facility.id,
                                          parseInt(e.target.value, 10) || 0
                                        )
                                      }
                                      className={`w-20 px-2 py-1 bg-white border rounded font-bold text-[#0B1F4B] text-center ${
                                        isInvalid ? 'border-[#D9480F] ring-1 ring-orange-500' : 'border-[#DBE5F5]'
                                      }`}
                                    />
                                    <span className="text-[#52627F]">boxes</span>
                                  </div>

                                  <button
                                    onClick={() => handleInitiateTransfer(candidate)}
                                    disabled={isInvalid}
                                    className="px-4 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 disabled:opacity-40 disabled:cursor-not-allowed rounded-xl shadow-xs flex items-center gap-1.5 transition-all cursor-pointer"
                                  >
                                    <span>Initiate Transfer Request</span>
                                    <ArrowRight className="w-3.5 h-3.5" />
                                  </button>
                                </div>

                                {/* Inline Validation Errors */}
                                {exceedsWaste && (
                                  <p className="text-[11px] font-semibold text-[#D9480F]">
                                    ⚠️ Quantity cannot exceed sender's projected waste ({selectedItem.forecast.projected_waste} boxes).
                                  </p>
                                )}
                                {exceedsNeed && (
                                  <p className="text-[11px] font-semibold text-[#D9480F]">
                                    ⚠️ Quantity cannot exceed recipient facility's estimated need ({candidate.estimated_need} boxes).
                                  </p>
                                )}
                              </div>
                            );
                          })()}
                        </div>
                      );
                    })}
                  </div>
                ) : (
                  <div className="p-8 text-center bg-[#F8FAFC] rounded-xl border border-[#DBE5F5] text-[#52627F] text-xs">
                    <p className="font-bold text-[#0B1F4B]">
                      No recipient facilities currently meet matching criteria.
                    </p>
                    <p className="mt-1">
                      This batch is either safe or neighboring facilities currently hold adequate buffer stock.
                    </p>
                  </div>
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
