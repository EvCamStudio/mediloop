import React from 'react';

export const Footer: React.FC = () => {
  return (
    <footer className="bg-white border-t border-[#DBE5F5] mt-12 py-5 text-xs text-[#52627F] font-body">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        {/* Brand & Description */}
        <div className="flex flex-wrap items-center gap-3 text-center md:text-left">
          <div className="flex items-center gap-2">
            <div className="w-6 h-6 rounded-md bg-[#1D4ED8] text-white flex items-center justify-center font-black text-xs">
              ML
            </div>
            <span className="font-extrabold text-[#0B1F4B] text-sm font-heading">
              MediLoop
            </span>
          </div>
          <span className="hidden md:inline text-[#DBE5F5]">|</span>
          <p className="text-xs text-[#52627F]">
            Decision support system for proactive medicine waste prevention and official inter-facility redistribution.
          </p>
        </div>

        {/* Badges & Compliance */}
        <div className="flex flex-wrap items-center gap-2 justify-center md:justify-end text-[11px] font-semibold">
          <span className="px-2.5 py-0.5 rounded-full bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
            SDG 3: Good Health
          </span>
          <span className="px-2.5 py-0.5 rounded-full bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
            SDG 12: Responsible Consumption
          </span>
          <span className="text-[#52627F] text-xs">
            Non-commercial decision support tool. No patient data is collected.
          </span>
          <span className="text-xs text-[#1D4ED8] bg-[#EAF1FF] px-2 py-0.5 rounded border border-[#DBE5F5]">
            All data shown is simulated for demonstration.
          </span>
        </div>
      </div>
    </footer>
  );
};
