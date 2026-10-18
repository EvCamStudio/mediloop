import React from 'react';
import { Hero3DMedicine } from './Hero3DMedicine';
import {
  ShieldCheck,
  TrendingDown,
  Sparkles,
  MapPin,
  ArrowRight,
  Activity,
  CheckCircle2,
  FileText,
  Building2,
  Leaf,
  Layers,
  Heart,
  ChevronRight,
} from 'lucide-react';

interface LandingPageProps {
  onEnterApp: () => void;
  onOpenMatchDirectly: (batchId: string) => void;
  onOpenAbout: () => void;
}

export const LandingPage: React.FC<LandingPageProps> = ({
  onEnterApp,
  onOpenMatchDirectly,
  onOpenAbout,
}) => {
  return (
    <div className="space-y-20 pb-16 font-body">
      {/* Hero Section */}
      <section className="relative pt-6 sm:pt-12 overflow-hidden">
        {/* Subtle background glow */}
        <div className="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[450px] bg-gradient-to-tr from-blue-100/60 via-sky-100/40 to-transparent rounded-full blur-3xl pointer-events-none -z-10" />

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            {/* Hero Left Content */}
            <div className="lg:col-span-6 space-y-6 text-center lg:text-left">
              {/* Main Headline */}
              <h1 className="text-4xl sm:text-5xl lg:text-[54px] font-extrabold text-[#0B1F4B] leading-[1.1] tracking-tight">
                Medicines Shouldn't Go to Waste. <br />
                <span className="text-[#1D4ED8]">They Belong with Patients.</span>
              </h1>

              {/* Subtitle */}
              <p className="text-base sm:text-lg text-[#52627F] max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                Intelligent decision support system to prevent expired medicine waste through
                proactive burn-rate forecasting, health facility network topology, and official inter-facility redistribution.
              </p>

              {/* Action Buttons */}
              <div className="flex flex-wrap items-center justify-center lg:justify-start gap-3.5 pt-2">
                <button
                  onClick={onEnterApp}
                  className="px-6 py-3.5 bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/25 flex items-center gap-2 transition-all hover:scale-[1.02] cursor-pointer"
                >
                  <span>Launch Workspace</span>
                  <ArrowRight className="w-4 h-4" />
                </button>

                <button
                  onClick={onOpenAbout}
                  className="px-5 py-3.5 bg-white hover:bg-slate-50 border border-[#DBE5F5] text-[#0B1F4B] font-semibold text-sm rounded-xl transition-all cursor-pointer shadow-xs"
                >
                  Explore Governance & Formulas
                </button>
              </div>

              {/* Value Badges */}
              <div className="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs font-semibold text-[#52627F]">
                <div className="flex items-center gap-2">
                  <div className="w-5 h-5 rounded-full bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center text-[10px] font-bold">
                    ✓
                  </div>
                  <span>100% Official Health Authority Protocol</span>
                </div>
                <div className="flex items-center gap-2">
                  <div className="w-5 h-5 rounded-full bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center text-[10px] font-bold">
                    ✓
                  </div>
                  <span>Zero Patient Data Ingested (Safe & Private)</span>
                </div>
                <div className="flex items-center gap-2">
                  <div className="w-5 h-5 rounded-full bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center text-[10px] font-bold">
                    ✓
                  </div>
                  <span>Non-Commercial Public Service</span>
                </div>
              </div>
            </div>

            {/* Hero Right 3D Visual */}
            <div className="lg:col-span-6 relative">
              <Hero3DMedicine
                onFindMatchClick={() => onOpenMatchDirectly('batch_amx_09')}
              />
            </div>
          </div>
        </div>
      </section>

      {/* 3 Step Workflow Section */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto space-y-2">
          <span className="text-xs font-bold uppercase tracking-wider text-[#1D4ED8] bg-[#EAF1FF] px-3 py-1 rounded-full border border-[#DBE5F5]">
            Redistribution Architecture
          </span>
          <h2 className="text-2xl sm:text-3xl font-extrabold text-[#0B1F4B]">
            How MediLoop Prevents Medicine Expiry Waste
          </h2>
          <p className="text-xs sm:text-sm text-[#52627F]">
            The algorithmic system forecasts, certified pharmacists and health authorities approve, and official logistics deliver.
          </p>
        </div>

        <div className="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 relative">
          {/* Step 1 */}
          <div className="bg-white rounded-2xl p-6 border border-[#DBE5F5] shadow-xs space-y-4 hover:shadow-md transition-shadow relative">
            <div className="w-12 h-12 rounded-2xl bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-extrabold text-lg">
              01
            </div>
            <h3 className="text-base font-bold text-[#0B1F4B]">
              Stock Ingestion & Daily Expiry Forecasting
            </h3>
            <p className="text-xs text-[#52627F] leading-relaxed">
              Staff ingest batch stock via CSV or daily updates. The algorithm computes the{' '}
              <b>Burn Rate</b> (daily consumption velocity) against remaining days to expiry to proactively detect at-risk batches.
            </p>
            <div className="text-xs font-mono text-[#1D4ED8] bg-[#EAF1FF] p-2.5 rounded-lg border border-[#DBE5F5]">
              Projected Waste = max(0, Stock - Burn Rate × Days to Expiry)
            </div>
          </div>

          {/* Step 2 */}
          <div className="bg-white rounded-2xl p-6 border border-[#DBE5F5] shadow-xs space-y-4 hover:shadow-md transition-shadow relative">
            <div className="w-12 h-12 rounded-2xl bg-[#EAF1FF] text-[#D9480F] flex items-center justify-center font-extrabold text-lg">
              02
            </div>
            <h3 className="text-base font-bold text-[#0B1F4B]">
              Smart Matching & Network Topology
            </h3>
            <p className="text-xs text-[#52627F] leading-relaxed">
              MediLoop matches surplus with nearby health facilities facing shortages. Scores are
              transparently ranked based on <b>50% Stock Deficit</b>, <b>30% Geographic Distance</b> (Haversine), and{' '}
              <b>20% Shelf-Life Feasibility</b>.
            </p>
            <div className="text-xs font-mono text-[#D9480F] bg-[#F8FAFC] p-2.5 rounded-lg border border-[#DBE5F5]">
              Score = 50% Deficit + 30% Distance + 20% Shelf Life
            </div>
          </div>

          {/* Step 3 */}
          <div className="bg-white rounded-2xl p-6 border border-[#DBE5F5] shadow-xs space-y-4 hover:shadow-md transition-shadow relative">
            <div className="w-12 h-12 rounded-2xl bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-extrabold text-lg">
              03
            </div>
            <h3 className="text-base font-bold text-[#0B1F4B]">
              Multi-Tier Approvals & Digital Delivery Note
            </h3>
            <p className="text-xs text-[#52627F] leading-relaxed">
              District Health Authority authorizes transfers with official electronic delivery notes (SBBK). Stock adjustments{' '}
              <b>only execute after recipient physically inspects and verifies intact delivery</b>.
            </p>
            <div className="text-xs font-mono text-[#1D4ED8] bg-[#EAF1FF] p-2.5 rounded-lg border border-[#DBE5F5]">
              Stock Updated ONLY When Status: Completed
            </div>
          </div>
        </div>
      </section>

      {/* Formula & Decision Support Transparency (PRD Section 9) */}
      <section className="bg-[#0B1F4B] text-white rounded-3xl p-8 sm:p-12 max-w-7xl mx-auto shadow-xl">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          <div className="lg:col-span-5 space-y-4">
            <span className="text-xs font-bold uppercase tracking-wider text-blue-300 bg-blue-900/60 px-3 py-1 rounded-full border border-blue-700">
              Algorithm Transparency (PRD Section 9)
            </span>
            <h2 className="text-2xl sm:text-3xl font-extrabold leading-tight">
              An Open, Auditable Decision Support System
            </h2>
            <p className="text-xs sm:text-sm text-slate-300 leading-relaxed">
              MediLoop is never a black box. Every match recommendation stems from a transparent mathematical model
              auditable by certified pharmacists and health regulators.
            </p>
            <div className="pt-2">
              <button
                onClick={onEnterApp}
                className="px-5 py-2.5 bg-[#1D4ED8] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors cursor-pointer"
              >
                Launch Workspace
              </button>
            </div>
          </div>

          <div className="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div className="bg-slate-800/80 p-4 rounded-xl border border-slate-700 space-y-1.5">
              <span className="text-blue-300 font-bold uppercase text-[10px]">Daily Consumption (Burn Rate)</span>
              <p className="text-slate-200 font-medium">
                Actual rolling daily consumption velocity (30–90 day window). If history is under 14 days, the system
                applies a <i>Limited data</i> warning tag.
              </p>
            </div>

            <div className="bg-slate-800/80 p-4 rounded-xl border border-slate-700 space-y-1.5">
              <span className="text-blue-300 font-bold uppercase text-[10px]">Days to Deplete</span>
              <p className="text-slate-200 font-medium">
                <code className="text-amber-300 font-mono">Stock ÷ Burn Rate</code>. Defines the natural consumption timeline
                for existing warehouse inventory.
              </p>
            </div>

            <div className="bg-slate-800/80 p-4 rounded-xl border border-slate-700 space-y-1.5">
              <span className="text-blue-300 font-bold uppercase text-[10px]">Projected Waste</span>
              <p className="text-slate-200 font-medium">
                <code className="text-orange-400 font-mono">max(0, Stock - (Burn Rate × Days to Expiry))</code>.
                When greater than 0, the batch triggers an immediate intervention alert.
              </p>
            </div>

            <div className="bg-slate-800/80 p-4 rounded-xl border border-slate-700 space-y-1.5">
              <span className="text-blue-300 font-bold uppercase text-[10px]">Haversine Distance & Need Cap</span>
              <p className="text-slate-200 font-medium">
                Calculates precise inter-facility GPS distances and caps transfer volumes to prevent recipient secondary overstock.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Safety & Compliance Principles */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="bg-[#EAF1FF] border border-[#DBE5F5] rounded-3xl p-8 sm:p-10">
          <div className="flex flex-wrap items-center justify-between gap-6">
            <div className="max-w-2xl space-y-2">
              <div className="flex items-center gap-2 text-[#1D4ED8] font-bold text-sm">
                <ShieldCheck className="w-5 h-5 text-[#1D4ED8]" />
                <span>Regulatory Integrity & Patient Safety</span>
              </div>
              <h3 className="text-xl font-bold text-[#0B1F4B]">
                Designed in Compliance with National Pharmaceutical Standards
              </h3>
              <p className="text-xs sm:text-sm text-[#52627F] leading-relaxed">
                MediLoop does not facilitate financial transactions or drug reselling, nor does it generate diagnostic or prescribing advice.
                It is purely focused on public healthcare supply chain optimization and preventing expired medicine waste.
              </p>
            </div>

            <button
              onClick={onEnterApp}
              className="px-6 py-3.5 bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-md transition-all hover:scale-[1.02] cursor-pointer"
            >
              Enter Application Workspace
            </button>
          </div>
        </div>
      </section>
    </div>
  );
};
