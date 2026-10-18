import React from 'react';
import { useApp } from '../../context/AppContext';
import { computeBatchForecast, formatIDR } from '../../utils/calculations';
import {
  AlertTriangle,
  ArrowRight,
  TrendingUp,
  TrendingDown,
  Building2,
  Calendar,
  Sparkles,
  ArrowUpRight,
  ShieldCheck,
  MapPin,
  Clock,
  Layers,
  CheckCircle2,
  FileText,
  Truck,
  UserCheck,
} from 'lucide-react';

interface OverviewDashboardProps {
  onFindMatch: (batchId: string) => void;
  onOpenMap: () => void;
  onOpenTransfers: () => void;
  onOpenForecast: () => void;
}

export const OverviewDashboard: React.FC<OverviewDashboardProps> = ({
  onFindMatch,
  onOpenMap,
  onOpenTransfers,
  onOpenForecast,
}) => {
  const { facilities, batches, drugs, burnRates, currentFacility, impactMetrics, transfers } = useApp();

  // Batches for current active facility
  const facilityBatches = batches.filter((b) => b.facility_id === currentFacility.id);

  const forecastData = facilityBatches.map((b) => {
    const drug = drugs.find((d) => d.id === b.drug_id);
    const burnRateKey = `${b.facility_id}_${b.drug_id}`;
    const burnRate = burnRates[burnRateKey] || 2.0;
    const forecast = computeBatchForecast(b, burnRate, 30);
    return { batch: b, drug, forecast };
  });

  const highRiskCount = forecastData.filter((i) => i.forecast.risk_level === 'HIGH').length;
  const mediumRiskCount = forecastData.filter((i) => i.forecast.risk_level === 'MEDIUM').length;
  const watchRiskCount = forecastData.filter((i) => i.forecast.risk_level === 'WATCH').length;
  const atRiskTotal = highRiskCount + mediumRiskCount + watchRiskCount;

  // Decision Queue items (Pending transfers)
  const pendingTransfers = transfers.filter(
    (t) => t.status === 'Requested' || t.status === 'Pharmacist Approved' || t.status === 'District Approved'
  );

  // Recent Activity Feed (flatten all transfer events and sort by timestamp)
  const recentActivities = transfers
    .flatMap((t) =>
      t.history.map((h) => ({
        ...h,
        transferCode: t.transfer_code,
        transferStatus: t.status,
      }))
    )
    .sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime())
    .slice(0, 5);

  const lastUpdatedTime = 'Oct 18, 2026, 10:15 AM';

  return (
    <div className="space-y-6 font-body text-slate-800">
      {/* Top Header Strip with Facility and Last Updated Timestamp */}
      <div className="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-[#DBE5F5] shadow-xs">
        <div>
          <h2 className="text-xl font-black text-[#0B1F4B] tracking-tight">Overview Dashboard</h2>
          <p className="text-sm text-[#52627F]">
            Decision support system for proactive redistribution and medicine waste prevention
          </p>
        </div>
        <div className="flex items-center gap-2 text-xs font-semibold text-[#52627F] bg-[#EAF1FF] px-3 py-1.5 rounded-xl border border-[#DBE5F5]">
          <Clock className="w-4 h-4 text-[#1D4ED8]" />
          <span>Last updated: {lastUpdatedTime}</span>
        </div>
      </div>

      {/* 4 Primary Top KPI Cards (Equal font size & period comparisons) */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* KPI 1: At-Risk Batches */}
        <div className="bg-white rounded-2xl p-5 border border-[#DBE5F5] shadow-xs flex flex-col justify-between">
          <div>
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold text-[#52627F] uppercase tracking-wider">
                At-Risk Batches
              </span>
              <div className="w-8 h-8 rounded-xl bg-[#EAF1FF] text-[#D9480F] flex items-center justify-center font-bold">
                <AlertTriangle className="w-4 h-4 text-[#D9480F]" />
              </div>
            </div>
            <div className="mt-3">
              <span className="text-2xl font-black text-[#0B1F4B] tracking-tight">
                {atRiskTotal} Batches
              </span>
            </div>
          </div>
          <div className="mt-3 pt-2.5 border-t border-[#DBE5F5] flex items-center justify-between text-xs">
            <span className="inline-flex items-center gap-1 font-bold text-[#1D4ED8] bg-[#EAF1FF] px-2 py-0.5 rounded-md">
              <TrendingDown className="w-3 h-3" /> -12% vs last month
            </span>
            <span className="font-semibold text-[#D9480F]">
              {highRiskCount} High Risk
            </span>
          </div>
        </div>

        {/* KPI 2: Redistributed Medicine */}
        <div className="bg-white rounded-2xl p-5 border border-[#DBE5F5] shadow-xs flex flex-col justify-between">
          <div>
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold text-[#52627F] uppercase tracking-wider">
                Redistributed Medicine
              </span>
              <div className="w-8 h-8 rounded-xl bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold">
                <Sparkles className="w-4 h-4" />
              </div>
            </div>
            <div className="mt-3">
              <span className="text-2xl font-black text-[#0B1F4B] tracking-tight">
                {(impactMetrics.total_units_saved + 1220).toLocaleString('en-US')} Boxes
              </span>
            </div>
          </div>
          <div className="mt-3 pt-2.5 border-t border-[#DBE5F5] flex items-center justify-between text-xs">
            <span className="inline-flex items-center gap-1 font-bold text-[#1D4ED8] bg-[#EAF1FF] px-2 py-0.5 rounded-md">
              <TrendingUp className="w-3 h-3" /> +18% vs monthly target
            </span>
            <span className="font-semibold text-[#1D4ED8]">
              SDG 3 & 12
            </span>
          </div>
        </div>

        {/* KPI 3: Prevented Waste Value */}
        <div className="bg-white rounded-2xl p-5 border border-[#DBE5F5] shadow-xs flex flex-col justify-between">
          <div>
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold text-[#52627F] uppercase tracking-wider">
                Prevented Waste Value
              </span>
              <div className="w-8 h-8 rounded-xl bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold">
                <TrendingUp className="w-4 h-4" />
              </div>
            </div>
            <div className="mt-3">
              <span className="text-2xl font-black text-[#0B1F4B] tracking-tight">
                {formatIDR(impactMetrics.total_value_saved_idr + 36280000)}
              </span>
            </div>
          </div>
          <div className="mt-3 pt-2.5 border-t border-[#DBE5F5] flex items-center justify-between text-xs">
            <span className="inline-flex items-center gap-1 font-bold text-[#1D4ED8] bg-[#EAF1FF] px-2 py-0.5 rounded-md">
              <TrendingUp className="w-3 h-3" /> +Rp 8,420,000 vs last month
            </span>
            <span className="font-semibold text-[#52627F]">
              Oct 2026
            </span>
          </div>
        </div>

        {/* KPI 4: Active Network Partners */}
        <div className="bg-white rounded-2xl p-5 border border-[#DBE5F5] shadow-xs flex flex-col justify-between">
          <div>
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold text-[#52627F] uppercase tracking-wider">
                Active Network Partners
              </span>
              <div className="w-8 h-8 rounded-xl bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold">
                <Building2 className="w-4 h-4" />
              </div>
            </div>
            <div className="mt-3">
              <span className="text-2xl font-black text-[#0B1F4B] tracking-tight">
                {facilities.length + 18} Facilities
              </span>
            </div>
          </div>
          <div className="mt-3 pt-2.5 border-t border-[#DBE5F5] flex items-center justify-between text-xs">
            <span className="inline-flex items-center gap-1 font-bold text-[#1D4ED8] bg-[#EAF1FF] px-2 py-0.5 rounded-md">
              +3 facilities joined this month
            </span>
            <span className="font-semibold text-[#52627F]">
              Medan District
            </span>
          </div>
        </div>
      </div>

      {/* Main Grid: At-Risk Table & Map Widget */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {/* Left Column: At-Risk Forecast Table (8 cols on lg) */}
        <div className="lg:col-span-8 bg-white rounded-2xl border border-[#DBE5F5] p-5 shadow-xs space-y-4">
          <div className="flex flex-wrap items-center justify-between gap-3 border-b border-[#DBE5F5] pb-3.5">
            <div>
              <h3 className="text-base font-bold text-[#0B1F4B]">
                At-Risk Expiry Forecast & Match Recommendation
              </h3>
              <p className="text-xs text-[#52627F] mt-0.5">
                Batches projected to expire before stock depletion based on current daily consumption
              </p>
            </div>

            {/* Short Legend under column header area */}
            <div className="flex items-center gap-3 text-xs bg-[#EAF1FF] px-3 py-1.5 rounded-lg border border-[#DBE5F5]">
              <div className="flex items-center gap-1.5">
                <span className="w-2.5 h-2.5 rounded-full bg-[#1D4ED8]"></span>
                <span className="text-[#0B1F4B] font-medium">Blue: consumed in time</span>
              </div>
              <div className="flex items-center gap-1.5">
                <span className="w-2.5 h-2.5 rounded-full bg-[#D9480F]"></span>
                <span className="text-[#0B1F4B] font-medium">Orange: at risk after expiry</span>
              </div>
            </div>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="bg-[#F8FAFC] text-[#52627F] font-bold border-b border-[#DBE5F5] text-xs">
                <tr>
                  <th className="py-2.5 px-3 whitespace-nowrap">Medicine Name</th>
                  <th className="py-2.5 px-3 min-w-[130px] whitespace-nowrap">Batch #</th>
                  <th className="py-2.5 px-3 whitespace-nowrap">Stock</th>
                  <th className="py-2.5 px-3 min-w-[220px]">Days to Deplete vs Expiry</th>
                  <th className="py-2.5 px-3 text-center min-w-[110px] whitespace-nowrap">Status</th>
                  <th className="py-2.5 px-3 text-right whitespace-nowrap">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-[#DBE5F5] text-sm">
                {forecastData.slice(0, 5).map(({ batch, drug, forecast }) => {
                  const maxDays = Math.max(forecast.days_to_deplete, forecast.days_to_expiry, 100);
                  const expiryPct = Math.min(100, (forecast.days_to_expiry / maxDays) * 100);
                  const depletePct = Math.min(100, (forecast.days_to_deplete / maxDays) * 100);
                  const isHigh = forecast.risk_level === 'HIGH';
                  const isMedium = forecast.risk_level === 'MEDIUM';
                  const isWatch = forecast.risk_level === 'WATCH';
                  const isSafe = forecast.risk_level === 'SAFE';
                  const isAtRisk = forecast.projected_waste > 0;

                  return (
                    <tr key={batch.id} className="hover:bg-[#F8FAFC] transition-colors">
                      {/* Medicine Name */}
                      <td className="py-3 px-3">
                        <p className="font-bold text-[#0B1F4B] whitespace-nowrap">{drug?.name}</p>
                        <p className="text-xs text-[#52627F] whitespace-nowrap">{drug?.category}</p>
                      </td>

                      {/* Batch # (No wrapping!) */}
                      <td className="py-3 px-3 font-mono font-bold text-[#0B1F4B] whitespace-nowrap">
                        #{batch.batch_no}
                      </td>

                      {/* Stock */}
                      <td className="py-3 px-3 whitespace-nowrap">
                        <span className="font-bold text-[#0B1F4B]">{batch.stock}</span>{' '}
                        <span className="text-[#52627F]">boxes</span>
                      </td>

                      {/* Progress Track */}
                      <td className="py-3 px-3">
                        <div className="space-y-1">
                          <div className="flex justify-between text-xs font-semibold">
                            <span className="text-[#D9480F]">Expires: {forecast.days_to_expiry}d</span>
                            <span className="text-[#1D4ED8]">Runs out: {forecast.days_to_deplete}d</span>
                          </div>
                          <div className="h-2 bg-[#F1F5F9] rounded-full flex overflow-hidden">
                            <div className="h-full bg-[#1D4ED8]" style={{ width: `${depletePct}%` }} />
                            {isAtRisk && (
                              <div className="h-full bg-[#D9480F]" style={{ width: `${Math.max(0, depletePct - expiryPct)}%` }} />
                            )}
                          </div>
                          {isAtRisk ? (
                            <p className="text-[11px] font-bold text-[#D9480F]">
                              Surplus: {forecast.projected_waste} boxes waste ({Math.round(forecast.waste_ratio * 100)}%)
                            </p>
                          ) : (
                            <p className="text-[11px] font-semibold text-[#1D4ED8]">
                              Stock depletes safely before expiry
                            </p>
                          )}
                        </div>
                      </td>

                      {/* Status Badge (No wrapping!) */}
                      <td className="py-3 px-3 text-center whitespace-nowrap">
                        {isHigh && (
                          <span className="inline-block px-2.5 py-1 rounded text-xs font-black uppercase tracking-wider bg-[#D9480F] text-white whitespace-nowrap">
                            HIGH RISK
                          </span>
                        )}
                        {isMedium && (
                          <span className="inline-block px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider bg-[#B7791F] text-white whitespace-nowrap">
                            MEDIUM RISK
                          </span>
                        )}
                        {isWatch && (
                          <span className="inline-block px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] whitespace-nowrap">
                            WATCH
                          </span>
                        )}
                        {isSafe && (
                          <span className="inline-block px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider border border-[#1D4ED8] text-[#1D4ED8] bg-white whitespace-nowrap">
                            SAFE
                          </span>
                        )}
                      </td>

                      {/* Action Button: Solid blue only on High & Medium/Watch rows */}
                      <td className="py-3 px-3 text-right whitespace-nowrap">
                        {isAtRisk ? (
                          <button
                            onClick={() => onFindMatch(batch.id)}
                            className="px-3.5 py-1.5 bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold text-xs rounded-lg shadow-xs transition-all hover:scale-[1.02] cursor-pointer"
                          >
                            Find Match
                          </button>
                        ) : (
                          <button
                            disabled
                            className="px-3 py-1.5 border border-[#DBE5F5] text-[#52627F] font-semibold text-xs rounded-lg opacity-70 cursor-default"
                          >
                            View details
                          </button>
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>

          <div className="pt-2 flex justify-end">
            <button
              onClick={onOpenForecast}
              className="text-xs font-bold text-[#1D4ED8] hover:underline flex items-center gap-1 cursor-pointer"
            >
              <span>View Full Expiry Forecast ({facilityBatches.length} Batches)</span>
              <ArrowUpRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>

        {/* Right Column: Mini Interactive Map & Match Suggestions */}
        <div className="lg:col-span-4 bg-white rounded-2xl border border-[#DBE5F5] p-5 shadow-xs space-y-4 flex flex-col justify-between">
          <div>
            <div className="flex items-center justify-between border-b border-[#DBE5F5] pb-3">
              <h3 className="text-base font-bold text-[#0B1F4B] flex items-center gap-2">
                <MapPin className="w-4 h-4 text-[#1D4ED8]" />
                <span>Interactive Network Map</span>
              </h3>
              <button
                onClick={onOpenMap}
                className="text-xs font-bold text-[#1D4ED8] hover:underline cursor-pointer"
              >
                Open Full Map →
              </button>
            </div>

            {/* Map Preview Graphic with Non-Obtrusive Hover Badge */}
            <div
              onClick={onOpenMap}
              className="mt-3.5 h-44 rounded-xl overflow-hidden relative cursor-pointer group border border-[#DBE5F5] bg-[#F8FAFC]"
              title="Click to explore interactive facility network map"
            >
              {/* GIS Grid pattern */}
              <div
                className="absolute inset-0 bg-cover bg-center transition-transform group-hover:scale-105 duration-500"
                style={{
                  backgroundImage:
                    'radial-gradient(#93c5fd 1.2px, transparent 1.2px), radial-gradient(#bfdbfe 1.2px, #f8fafc 1.2px)',
                  backgroundSize: '20px 20px',
                  backgroundPosition: '0 0, 10px 10px',
                }}
              />

              {/* Geodesic Connecting Arc Line */}
              <svg className="absolute inset-0 w-full h-full pointer-events-none">
                <path
                  d="M 65 45 Q 160 85 245 115"
                  stroke="#1D4ED8"
                  strokeWidth="2.5"
                  strokeDasharray="5,5"
                  fill="none"
                />
              </svg>

              {/* Node 1: Puskesmas Simpang (Surplus) */}
              <div className="absolute top-5 left-5 bg-[#D9480F] text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 border-2 border-white">
                <span>⚠️ Pusk. Simpang</span>
              </div>

              {/* Unobstructed Distance Badge */}
              <div className="absolute top-12 left-28 bg-white text-[#1D4ED8] border border-[#DBE5F5] text-[10px] font-extrabold px-2 py-0.5 rounded shadow-xs pointer-events-none">
                4.8 km (Haversine)
              </div>

              {/* Node 2: Puskesmas Medan Baru (Deficit) */}
              <div className="absolute bottom-6 right-6 bg-[#1D4ED8] text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 border-2 border-white">
                <span>🏥 Pusk. Medan Baru</span>
              </div>

              {/* Small Non-Covering Subtle Hover Chip at Bottom Corner */}
              <div className="absolute bottom-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                <span className="bg-white/95 text-[#1D4ED8] text-[10px] font-bold px-2.5 py-1 rounded-md border border-[#DBE5F5] shadow-xs flex items-center gap-1">
                  <span>Explore Network GIS</span>
                  <span>↗</span>
                </span>
              </div>
            </div>
          </div>

          {/* Quick Match Candidates Box */}
          <div className="space-y-2 pt-2 border-t border-[#DBE5F5]">
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold text-[#0B1F4B] uppercase tracking-wider block">
                Top Match Suggestions
              </span>
              <span className="text-[11px] text-[#52627F] font-semibold">AI Match Score</span>
            </div>

            {/* Candidate 1 */}
            <div className="p-3 rounded-xl bg-[#F8FAFC] border border-[#DBE5F5] space-y-1.5">
              <div className="flex items-center justify-between">
                <div>
                  <h4 className="font-bold text-xs text-[#0B1F4B]">1. Puskesmas Medan Baru</h4>
                  <p className="text-xs text-[#52627F]">
                    Match Score: <span className="font-bold text-[#1D4ED8]">92%</span> • Needs 200 boxes • 4.8 km
                  </p>
                </div>
                <button
                  onClick={() => onFindMatch('batch_amx_09')}
                  className="px-3 py-1.5 bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold rounded-lg shadow-xs transition-colors cursor-pointer"
                >
                  Create Transfer
                </button>
              </div>
            </div>

            {/* Candidate 2 */}
            <div className="p-3 rounded-xl bg-[#F8FAFC] border border-[#DBE5F5] flex items-center justify-between">
              <div>
                <h4 className="font-bold text-xs text-[#0B1F4B]">2. Klinik Pratama Sentosa</h4>
                <p className="text-xs text-[#52627F]">
                  Match Score: <span className="font-bold text-[#1D4ED8]">81%</span> • Needs 100 boxes • 7.2 km
                </p>
              </div>
              <button
                onClick={onOpenMap}
                className="px-3 py-1.5 bg-white border border-[#DBE5F5] hover:bg-[#F8FAFC] text-[#0B1F4B] text-xs font-semibold rounded-lg transition-colors cursor-pointer"
              >
                Route Map
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Bottom Grid: PRD Decision Queue & Recent Activity Feed */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Section 1: Decision Queue (Pending Approvals) */}
        <div className="bg-white rounded-2xl border border-[#DBE5F5] p-5 shadow-xs space-y-3">
          <div className="flex items-center justify-between border-b border-[#DBE5F5] pb-3">
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-[#1D4ED8]" />
              <h3 className="text-base font-bold text-[#0B1F4B]">Decision Queue (Pending Approvals)</h3>
            </div>
            <span className="px-2 py-0.5 rounded-full text-xs font-bold bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
              {pendingTransfers.length} Action Needed
            </span>
          </div>

          <div className="divide-y divide-[#DBE5F5]">
            {pendingTransfers.map((t) => {
              const drug = drugs.find((d) => d.id === t.drug_id);
              const targetFacility = facilities.find((f) => f.id === t.to_facility_id);

              return (
                <div key={t.id} className="py-3 flex flex-wrap items-center justify-between gap-3 text-sm">
                  <div>
                    <div className="flex items-center gap-2">
                      <span className="font-mono text-xs font-bold text-[#1D4ED8] bg-[#EAF1FF] px-2 py-0.5 rounded">
                        {t.transfer_code}
                      </span>
                      <span className="font-bold text-[#0B1F4B]">{drug?.name}</span>
                    </div>
                    <p className="text-xs text-[#52627F] mt-1">
                      Recipient: <b>{targetFacility?.name}</b> • Quantity: <b>{t.qty} boxes</b>
                    </p>
                    <p className="text-[11px] text-[#52627F] mt-0.5">
                      Status: <span className="font-semibold text-[#B7791F]">{t.status}</span>
                    </p>
                  </div>

                  <button
                    onClick={onOpenTransfers}
                    className="px-3.5 py-1.5 text-xs font-bold text-[#1D4ED8] bg-[#EAF1FF] hover:bg-blue-100 rounded-lg border border-[#DBE5F5] transition-colors cursor-pointer"
                  >
                    Review in Transfers →
                  </button>
                </div>
              );
            })}

            {pendingTransfers.length === 0 && (
              <div className="py-8 text-center text-[#52627F] text-xs">
                No pending transfer requests requiring decision at this time.
              </div>
            )}
          </div>
        </div>

        {/* Section 2: Recent Activity Feed */}
        <div className="bg-white rounded-2xl border border-[#DBE5F5] p-5 shadow-xs space-y-3">
          <div className="flex items-center justify-between border-b border-[#DBE5F5] pb-3">
            <div className="flex items-center gap-2">
              <Clock className="w-4 h-4 text-[#1D4ED8]" />
              <h3 className="text-base font-bold text-[#0B1F4B]">Recent Activity Feed</h3>
            </div>
            <button
              onClick={onOpenTransfers}
              className="text-xs font-bold text-[#1D4ED8] hover:underline cursor-pointer"
            >
              Full History →
            </button>
          </div>

          <div className="divide-y divide-[#DBE5F5]">
            {recentActivities.map((act) => (
              <div key={act.id} className="py-3 flex items-start gap-3 text-sm">
                <div className="w-8 h-8 rounded-full bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                  ✓
                </div>
                <div className="min-w-0 flex-1">
                  <p className="text-sm font-bold text-[#0B1F4B]">{act.action}</p>
                  <p className="text-xs text-[#52627F] mt-0.5">
                    By <b>{act.actor_name}</b> ({act.actor_role}) • <span className="font-mono">{act.transferCode}</span>
                  </p>
                  {act.note && (
                    <p className="text-xs text-[#52627F] italic mt-1 bg-[#F8FAFC] p-1.5 rounded border border-[#DBE5F5]">
                      "{act.note}"
                    </p>
                  )}
                  <p className="text-[11px] text-[#52627F] mt-1">
                    {new Date(act.timestamp).toLocaleDateString('en-US', {
                      month: 'short',
                      day: 'numeric',
                      year: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit',
                    })}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};
