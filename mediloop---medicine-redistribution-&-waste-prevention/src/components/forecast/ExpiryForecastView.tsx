import React, { useState, useMemo } from 'react';
import { useApp } from '../../context/AppContext';
import { RiskLevel } from '../../types';
import { computeBatchForecast } from '../../utils/calculations';
import {
  AlertTriangle,
  ArrowRight,
  Search,
  Filter,
  ArrowUpDown,
  Clock,
  Info,
  ChevronDown,
  ChevronUp,
  Snowflake,
  ShieldAlert,
} from 'lucide-react';

interface ExpiryForecastViewProps {
  onFindMatch: (batchId: string) => void;
}

export const ExpiryForecastView: React.FC<ExpiryForecastViewProps> = ({ onFindMatch }) => {
  const { batches, drugs, burnRates, currentFacility } = useApp();

  const [searchQuery, setSearchQuery] = useState<string>('');
  const [filterRisk, setFilterRisk] = useState<string>('all');
  const [sortBy, setSortBy] = useState<string>('waste_desc');
  const [showThresholds, setShowThresholds] = useState<boolean>(false);

  // Batches for current facility
  const facilityBatches = batches.filter((b) => b.facility_id === currentFacility.id);

  const forecastData = useMemo(() => {
    return facilityBatches.map((b) => {
      const drug = drugs.find((d) => d.id === b.drug_id);
      const burnRateKey = `${b.facility_id}_${b.drug_id}`;
      const burnRate = burnRates[burnRateKey] || 2.0;
      const forecast = computeBatchForecast(b, burnRate, 30);
      return { batch: b, drug, forecast };
    });
  }, [facilityBatches, drugs, burnRates]);

  // Summary statistics for the compact one-line strip
  const summaryStats = useMemo(() => {
    const atRisk = forecastData.filter((i) => i.forecast.risk_level !== 'SAFE');
    const totalWaste = atRisk.reduce((acc, i) => acc + i.forecast.projected_waste, 0);
    const high = forecastData.filter((i) => i.forecast.risk_level === 'HIGH').length;
    const med = forecastData.filter((i) => i.forecast.risk_level === 'MEDIUM').length;
    const watch = forecastData.filter((i) => i.forecast.risk_level === 'WATCH').length;
    return {
      atRiskCount: atRisk.length,
      totalWaste,
      highCount: high,
      medCount: med,
      watchCount: watch,
    };
  }, [forecastData]);

  // Filter & Sort
  const filteredAndSortedData = useMemo(() => {
    const filtered = forecastData.filter((item) => {
      const matchesSearch =
        item.drug?.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        item.batch.batch_no.toLowerCase().includes(searchQuery.toLowerCase());
      const matchesRisk = filterRisk === 'all' || item.forecast.risk_level === filterRisk;
      return matchesSearch && matchesRisk;
    });

    const riskOrder: Record<RiskLevel, number> = {
      HIGH: 4,
      MEDIUM: 3,
      WATCH: 2,
      SAFE: 1,
    };

    return filtered.sort((a, b) => {
      if (sortBy === 'waste_desc') {
        return b.forecast.projected_waste - a.forecast.projected_waste;
      }
      if (sortBy === 'risk_level') {
        return riskOrder[b.forecast.risk_level] - riskOrder[a.forecast.risk_level];
      }
      if (sortBy === 'expiry_asc') {
        return a.forecast.days_to_expiry - b.forecast.days_to_expiry;
      }
      if (sortBy === 'deplete_desc') {
        return b.forecast.days_to_deplete - a.forecast.days_to_deplete;
      }
      return 0;
    });
  }, [forecastData, searchQuery, filterRisk, sortBy]);

  const lastUpdatedTime = 'Oct 18, 2026, 10:15 AM';

  return (
    <div className="space-y-4 font-body text-slate-800">
      {/* 1. Compact One-Line Summary Strip (Replaces bulky header + legend) */}
      <div className="bg-[#EAF1FF] border border-[#DBE5F5] rounded-xl px-4 py-3 flex flex-wrap items-center justify-between gap-3 text-sm">
        <div className="flex items-center gap-2.5 font-bold text-[#0B1F4B]">
          <span className="w-2.5 h-2.5 rounded-full bg-[#D9480F]"></span>
          <span>
            {summaryStats.atRiskCount} at-risk batches • {summaryStats.totalWaste} boxes projected waste •{' '}
            {summaryStats.highCount} High, {summaryStats.medCount} Medium, {summaryStats.watchCount} Watch
          </span>
        </div>
        <div className="flex items-center gap-2 text-xs font-semibold text-[#52627F]">
          <Clock className="w-3.5 h-3.5 text-[#1D4ED8]" />
          <span>Last updated: {lastUpdatedTime}</span>
        </div>
      </div>

      {/* 2. Unified Controls Bar on ONE single row: Search, Risk Filter, and Sort */}
      <div className="bg-white p-3.5 rounded-xl border border-[#DBE5F5] shadow-xs flex flex-wrap items-center justify-between gap-3 text-sm">
        <div className="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
          {/* Search */}
          <div className="flex items-center gap-2 bg-[#F8FAFC] border border-[#DBE5F5] rounded-lg px-3 py-1.5 w-full sm:w-64">
            <Search className="w-4 h-4 text-[#52627F]" />
            <input
              type="text"
              placeholder="Search drug or batch #..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="bg-transparent text-sm text-[#0B1F4B] placeholder-[#52627F] outline-none w-full"
            />
          </div>

          {/* Risk Level Filter */}
          <div className="flex items-center gap-2">
            <span className="text-xs font-semibold text-[#52627F]">Risk level:</span>
            <select
              value={filterRisk}
              onChange={(e) => setFilterRisk(e.target.value)}
              className="text-sm bg-[#F8FAFC] border border-[#DBE5F5] rounded-lg px-3 py-1.5 text-[#0B1F4B] font-medium outline-none"
            >
              <option value="all">All Risk Levels</option>
              <option value="HIGH">High Risk (≥ 50%)</option>
              <option value="MEDIUM">Medium Risk (20–49%)</option>
              <option value="WATCH">Watch (1–19%)</option>
              <option value="SAFE">Safe (0%)</option>
            </select>
          </div>
        </div>

        {/* Sort Control */}
        <div className="flex items-center gap-2 text-sm">
          <span className="text-xs font-semibold text-[#52627F] flex items-center gap-1">
            <ArrowUpDown className="w-3.5 h-3.5 text-[#1D4ED8]" />
            <span>Sort by:</span>
          </span>
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            className="text-sm bg-[#F8FAFC] border border-[#DBE5F5] rounded-lg px-3 py-1.5 font-bold text-[#0B1F4B] outline-none"
          >
            <option value="waste_desc">Projected waste (High to Low)</option>
            <option value="risk_level">Risk level (High first)</option>
            <option value="expiry_asc">Days to expiry (Fewest first)</option>
            <option value="deplete_desc">Days to deplete (Longest first)</option>
          </select>
        </div>
      </div>

      {/* 3. Small Collapsible Risk Thresholds Note */}
      <div className="text-xs text-[#52627F] flex flex-wrap items-center justify-between px-1 gap-2">
        <button
          onClick={() => setShowThresholds(!showThresholds)}
          className="flex items-center gap-1 font-semibold text-[#1D4ED8] hover:underline cursor-pointer"
        >
          <Info className="w-3.5 h-3.5" />
          <span>Risk thresholds definition</span>
          {showThresholds ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />}
        </button>

        {showThresholds && (
          <div className="w-full bg-[#EAF1FF] text-[#0B1F4B] border border-[#DBE5F5] rounded-lg p-2.5 text-xs flex flex-wrap items-center gap-4">
            <span><b>High Risk:</b> Waste ratio ≥ 50%</span>
            <span>•</span>
            <span><b>Medium Risk:</b> Waste ratio 20–49%</span>
            <span>•</span>
            <span><b>Watch:</b> Waste ratio 1–19%</span>
            <span>•</span>
            <span><b>Safe:</b> Waste ratio 0% (stock depletes before expiry)</span>
          </div>
        )}
      </div>

      {/* 4. Single Timeline Track per Batch (Compact rows: >= 4 visible on 1080p) */}
      <div className="bg-white rounded-xl border border-[#DBE5F5] shadow-xs divide-y divide-[#DBE5F5] overflow-hidden">
        {filteredAndSortedData.map(({ batch, drug, forecast }) => {
          const maxTimelineDays = Math.max(forecast.days_to_deplete, forecast.days_to_expiry, 60);
          const expiryPercent = Math.min(100, Math.max(5, (forecast.days_to_expiry / maxTimelineDays) * 100));
          const depletePercent = Math.min(100, Math.max(5, (forecast.days_to_deplete / maxTimelineDays) * 100));

          const isAtRisk = forecast.projected_waste > 0;
          const isHigh = forecast.risk_level === 'HIGH';
          const isMedium = forecast.risk_level === 'MEDIUM';
          const isWatch = forecast.risk_level === 'WATCH';
          const isSafe = forecast.risk_level === 'SAFE';

          // Single timeline calculation:
          // Stock consumed before expiry marker: min(days_to_deplete, days_to_expiry)
          // Surplus stock wasted after expiry marker: days_to_deplete > days_to_expiry
          const beforeExpiryDays = Math.min(forecast.days_to_deplete, forecast.days_to_expiry);
          const beforeExpiryWidthPct = (beforeExpiryDays / maxTimelineDays) * 100;
          const afterExpiryDays = Math.max(0, forecast.days_to_deplete - forecast.days_to_expiry);
          const afterExpiryWidthPct = (afterExpiryDays / maxTimelineDays) * 100;

          return (
            <div key={batch.id} className="p-4 hover:bg-[#F8FAFC] transition-colors">
              {/* Row Header: Drug Name, Batch #, Risk Badge, Right: Projected Waste & Action */}
              <div className="flex flex-wrap items-center justify-between gap-3 mb-2.5">
                <div className="flex flex-wrap items-center gap-2.5 min-w-0">
                  <h4 className="font-bold text-sm text-[#0B1F4B]">{drug?.name}</h4>
                  <span className="font-mono text-xs font-bold text-[#52627F] bg-[#F1F5F9] px-2 py-0.5 rounded">
                    #{batch.batch_no}
                  </span>

                  {/* Risk Badge as strictly specified */}
                  {isHigh && (
                    <span className="px-2.5 py-0.5 rounded text-xs font-black uppercase tracking-wider bg-[#D9480F] text-white">
                      HIGH RISK
                    </span>
                  )}
                  {isMedium && (
                    <span className="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-[#B7791F] text-white">
                      MEDIUM RISK
                    </span>
                  )}
                  {isWatch && (
                    <span className="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
                      WATCH
                    </span>
                  )}
                  {isSafe && (
                    <span className="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider border border-[#1D4ED8] text-[#1D4ED8] bg-white">
                      SAFE
                    </span>
                  )}

                  {/* Limited Data Badge (< 14 days usage history) */}
                  {forecast.is_limited_data && (
                    <span className="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#EAF1FF] text-[#0B1F4B] border border-[#DBE5F5]" title="Usage history < 14 days">
                      Limited data
                    </span>
                  )}

                  {/* Cold Chain Badge */}
                  {drug?.cold_chain && (
                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
                      <Snowflake className="w-3 h-3 text-[#1D4ED8]" />
                      <span>Cold chain (Special handling - excluded from auto-match)</span>
                    </span>
                  )}
                </div>

                {/* Right: Projected Waste Number & Find Recipient Button */}
                <div className="flex items-center gap-3 shrink-0">
                  <div className="text-right">
                    {isAtRisk ? (
                      <div>
                        <span className="text-[11px] font-bold uppercase tracking-wider text-[#D9480F] block leading-none">
                          Projected Waste
                        </span>
                        <span className="text-base font-black text-[#D9480F]">
                          {forecast.projected_waste} boxes ({Math.round(forecast.waste_ratio * 100)}%)
                        </span>
                      </div>
                    ) : (
                      <span className="text-xs font-bold text-[#1D4ED8] bg-[#EAF1FF] px-2.5 py-1 rounded-md border border-[#DBE5F5]">
                        Stock safe (0 waste)
                      </span>
                    )}
                  </div>

                  {/* Action button ONLY on High, Medium, Watch. Safe rows show muted outline */}
                  {isAtRisk ? (
                    <button
                      onClick={() => onFindMatch(batch.id)}
                      className={`px-3.5 py-1.5 text-xs font-bold rounded-lg shadow-xs transition-all hover:scale-[1.02] cursor-pointer flex items-center gap-1.5 ${
                        isHigh
                          ? 'bg-[#D9480F] hover:bg-orange-700 text-white'
                          : 'bg-[#1D4ED8] hover:bg-blue-800 text-white'
                      }`}
                    >
                      <span>Find Recipient</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </button>
                  ) : (
                    <button
                      disabled
                      className="px-3 py-1.5 text-xs font-semibold text-[#52627F] bg-[#F8FAFC] border border-[#DBE5F5] rounded-lg opacity-70 cursor-default"
                    >
                      View details
                    </button>
                  )}
                </div>
              </div>

              {/* 3. Single Horizontal Timeline Track on Shared Scale */}
              <div className="relative pt-6 pb-2">
                {/* Vertical Expiry Marker Line */}
                <div
                  className="absolute top-0 bottom-2 z-10 flex flex-col items-center pointer-events-none"
                  style={{ left: `${expiryPercent}%` }}
                >
                  <span className="text-[10px] font-bold text-[#0B1F4B] bg-white border border-[#DBE5F5] shadow-xs px-1.5 py-0.5 rounded whitespace-nowrap -translate-x-1/2">
                    Expires in {forecast.days_to_expiry} days ({batch.expiry_date})
                  </span>
                  <div className="w-0.5 h-full bg-[#0B1F4B] mt-1" />
                </div>

                {/* Track background */}
                <div className="h-4 bg-[#F1F5F9] rounded-md overflow-hidden relative flex">
                  {/* Part BEFORE expiry marker: Primary Blue #1D4ED8 (consumed in time) */}
                  <div
                    className="h-full bg-[#1D4ED8] flex items-center pl-2 text-[10px] text-white font-bold whitespace-nowrap overflow-hidden"
                    style={{ width: `${beforeExpiryWidthPct}%` }}
                    title={`Consumed in time: ${beforeExpiryDays} days`}
                  >
                    {beforeExpiryWidthPct > 15 && `Day 0 – ${beforeExpiryDays}`}
                  </div>

                  {/* Part AFTER expiry marker: Risk Orange #D9480F (projected waste) */}
                  {afterExpiryWidthPct > 0 && (
                    <div
                      className="h-full bg-[#D9480F] flex items-center justify-end pr-2 text-[10px] text-white font-extrabold whitespace-nowrap overflow-hidden"
                      style={{ width: `${afterExpiryWidthPct}%` }}
                      title={`Projected waste: ${forecast.projected_waste} boxes`}
                    >
                      {afterExpiryWidthPct > 12 && `${forecast.projected_waste} boxes waste`}
                    </div>
                  )}
                </div>

                {/* Timeline status label below track */}
                <div className="flex justify-between items-center text-xs mt-1.5 text-[#52627F]">
                  <span className="font-mono text-xs">
                    <b>{batch.stock} boxes</b> ÷ <b>{forecast.burn_rate}/day</b> = <b>{forecast.days_to_deplete} days to deplete</b>
                  </span>
                  {isAtRisk ? (
                    <span className="text-xs font-bold text-[#D9480F]">
                      Surplus: {forecast.projected_waste} boxes projected waste ({Math.round(forecast.waste_ratio * 100)}%)
                    </span>
                  ) : (
                    <span className="text-xs font-bold text-[#1D4ED8]">
                      Stock runs out before expiry ({forecast.days_to_expiry - forecast.days_to_deplete} days safety margin)
                    </span>
                  )}
                </div>
              </div>
            </div>
          );
        })}

        {filteredAndSortedData.length === 0 && (
          <div className="p-12 text-center text-[#52627F] text-sm">
            No medicine batches match your search criteria.
          </div>
        )}
      </div>
    </div>
  );
};
