import React from 'react';
import { X, ShieldCheck, Award, Heart, CheckCircle2, BookOpen } from 'lucide-react';

interface AboutModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export const AboutModal: React.FC<AboutModalProps> = ({ isOpen, onClose }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-[#DBE5F5] overflow-hidden font-body">
        {/* Header */}
        <div className="px-6 py-4 bg-[#F8FAFC] border-b border-[#DBE5F5] flex items-center justify-between">
          <div className="flex items-center gap-2">
            <ShieldCheck className="w-5 h-5 text-[#1D4ED8]" />
            <h3 className="text-base font-bold text-[#0B1F4B]">
              About MediLoop & Regulatory Framework
            </h3>
          </div>
          <button
            onClick={onClose}
            className="p-1.5 rounded-lg text-[#52627F] hover:text-[#0B1F4B] hover:bg-slate-200/60 transition-colors cursor-pointer"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Content */}
        <div className="p-6 overflow-y-auto space-y-5 text-xs text-[#0B1F4B] leading-relaxed flex-1">
          {/* Section 1 */}
          <div className="space-y-2">
            <h4 className="font-bold text-sm text-[#0B1F4B] flex items-center gap-1.5">
              <span className="w-2 h-2 rounded-full bg-[#1D4ED8]"></span>
              Background & Problem Statement
            </h4>
            <p className="text-[#52627F] text-xs leading-relaxed">
              Healthcare facilities frequently accumulate medicines that will not be depleted prior to expiry,
              while neighboring clinics simultaneously suffer severe stock-outs of the exact same essential treatments.
              Because inter-facility inventory logs are isolated in organizational silos, surplus remains invisible
              until it must be incinerated as hazardous waste.
            </p>
          </div>

          {/* Section 2: Mathematical Scenario Demonstration */}
          <div className="p-4 bg-[#EAF1FF] border border-[#DBE5F5] rounded-xl space-y-2">
            <h4 className="font-bold text-xs text-[#0B1F4B] uppercase tracking-wider">
              Mathematical Scenario Example (PRD Section 9)
            </h4>
            <div className="space-y-1 font-mono text-xs text-[#0B1F4B]">
              <p>• Current Stock: <b>480 boxes</b>, Average burn rate: <b>4 boxes/day</b></p>
              <p>• Days until batch expiry: <b>45 days</b></p>
              <p>• Days to stock depletion = 480 ÷ 4 = <b>120 days</b></p>
              <p className="text-[#D9480F] font-bold">
                • Projected waste = 480 - (4 × 45) = <b>300 boxes</b> (High Risk)
              </p>
              <p className="text-[#1D4ED8] font-bold">
                • Algorithmic Match: 200 boxes routed to Puskesmas Medan Baru (Score 92%) and 100 boxes to Klinik Sentosa
                (Score 81%), restoring projected waste back to zero!
              </p>
            </div>
          </div>

          {/* Section 3: Regulatory Pillars */}
          <div className="space-y-2">
            <h4 className="font-bold text-sm text-[#0B1F4B] flex items-center gap-1.5">
              <span className="w-2 h-2 rounded-full bg-[#1D4ED8]"></span>
              Compliance & Pharmaceutical Governance
            </h4>
            <ul className="space-y-1.5 list-disc pl-4 text-[#52627F] text-xs">
              <li>
                <b>Non-Commercial Decision Support:</b> MediLoop is a Decision Support System (DSS), not an
                open commercial marketplace. Zero financial transactions or drug reselling occur on this platform.
              </li>
              <li>
                <b>Human-in-the-Loop Multi-Tier Approval:</b> The system provides algorithmic recommendations,
                but every movement mandates explicit authorization from facility pharmacists and the District Health Pharmacy Unit.
              </li>
              <li>
                <b>Zero Patient Privacy Risk:</b> The platform processes strictly aggregate facility inventory logs.
                No patient identifiable information, prescription records, or medical history is ever ingested.
              </li>
            </ul>
          </div>

          {/* Section 4: Notice */}
          <div className="pt-2 border-t border-[#DBE5F5] flex flex-wrap items-center justify-between text-xs text-[#52627F] gap-2">
            <span className="text-[#1D4ED8] bg-[#EAF1FF] px-2.5 py-1 rounded font-semibold border border-[#DBE5F5]">
              All data shown is simulated for demonstration.
            </span>
            <span className="font-bold text-[#0B1F4B]">MediLoop Decision Support System</span>
          </div>
        </div>

        {/* Footer */}
        <div className="px-6 py-3 bg-[#F8FAFC] border-t border-[#DBE5F5] text-right">
          <button
            onClick={onClose}
            className="px-4 py-2 bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold text-xs rounded-xl transition-colors cursor-pointer"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  );
};
