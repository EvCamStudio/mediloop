import React, { useState } from 'react';
import { useApp } from '../../context/AppContext';
import { User, Home, Menu, Building, ShieldCheck } from 'lucide-react';

export type NavTab =
  | 'landing'
  | 'overview'
  | 'inventory'
  | 'forecast'
  | 'map'
  | 'matches'
  | 'transfers'
  | 'impact'
  | 'admin';

interface HeaderProps {
  currentTab: NavTab;
  onSelectTab: (tab: NavTab) => void;
  onToggleMobileSidebar?: () => void;
}

export const Header: React.FC<HeaderProps> = ({
  currentTab,
  onSelectTab,
  onToggleMobileSidebar,
}) => {
  const { currentRole, currentFacility } = useApp();
  const [isProfileOpen, setIsProfileOpen] = useState<boolean>(false);

  const getPageTitle = (tab: NavTab) => {
    switch (tab) {
      case 'overview':
        return 'Overview Dashboard';
      case 'forecast':
        return 'Expiry Forecast & Burn Rate';
      case 'map':
        return 'Interactive Network Map';
      case 'matches':
        return 'Match Recommendations';
      case 'transfers':
        return 'Transfers & Approvals';
      case 'inventory':
        return 'Inventory & Batch Stock';
      case 'impact':
        return 'Impact & Sustainability Reports';
      case 'admin':
        return 'System Settings & Verifications';
      default:
        return '';
    }
  };

  return (
    <header className="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-xs">
      <div className="w-full px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 gap-4">
          {/* Left Side */}
          <div className="flex items-center gap-3">
            {/* Mobile Hamburger toggle (visible in dashboard on mobile) */}
            {currentTab !== 'landing' && onToggleMobileSidebar && (
              <button
                onClick={onToggleMobileSidebar}
                className="p-2 rounded-xl text-slate-600 hover:bg-slate-100 md:hidden cursor-pointer"
                title="Open sidebar menu"
              >
                <Menu className="w-5 h-5" />
              </button>
            )}

            {/* Logo on Landing page, or Page Title in Dashboard */}
            {currentTab === 'landing' ? (
              <button
                onClick={() => onSelectTab('landing')}
                className="flex items-center gap-2.5 group cursor-pointer text-left"
              >
                <div className="w-9 h-9 rounded-xl bg-[#1D4ED8] text-white flex items-center justify-center font-black text-base shadow-md shadow-blue-600/30 group-hover:scale-105 transition-transform font-heading">
                  ML
                </div>
                <div>
                  <span className="text-lg font-black tracking-tight text-[#0B1F4B] block leading-none font-heading">
                    Medi<span className="text-[#1D4ED8]">Loop</span>
                  </span>
                  <span className="text-[10px] font-semibold text-[#52627F] block tracking-wider uppercase mt-0.5">
                    Medicine Redistribution
                  </span>
                </div>
              </button>
            ) : (
              <div>
                <h1 className="text-base sm:text-lg font-bold text-[#0B1F4B] leading-tight">
                  {getPageTitle(currentTab)}
                </h1>
                <p className="text-xs text-[#52627F] hidden sm:block">
                  Decision Support & Proactive Medicine Redistribution Network
                </p>
              </div>
            )}
          </div>

          {/* Right Action Area */}
          <div className="flex items-center gap-3">
            {/* "Go to Dashboard" button on landing page */}
            {currentTab === 'landing' && (
              <button
                onClick={() => onSelectTab('overview')}
                className="px-4 py-2 bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-blue-600/20 flex items-center gap-2 transition-all hover:scale-[1.03] cursor-pointer"
              >
                <Home className="w-4 h-4" />
                <span>Go to Dashboard</span>
              </button>
            )}

            {/* User Profile / Person Icon Button */}
            <button
              onClick={() => setIsProfileOpen(!isProfileOpen)}
              className="relative p-2.5 rounded-xl text-slate-700 bg-slate-100 hover:bg-[#EAF1FF] hover:text-[#1D4ED8] transition-colors cursor-pointer border border-[#DBE5F5] flex items-center gap-2"
              title="User Profile & Health Facility Status"
            >
              <User className="w-4 h-4 text-[#1D4ED8]" />
              <span className="text-xs font-bold text-[#0B1F4B] hidden sm:inline">
                {currentFacility.name.replace('Puskesmas ', 'Pusk. ').replace('Klinik Pratama ', '')}
              </span>
            </button>
          </div>
        </div>

        {/* Profile Card Dropdown Modal */}
        {isProfileOpen && (
          <div className="absolute right-4 sm:right-8 top-18 w-80 bg-white rounded-2xl p-5 shadow-2xl border border-[#DBE5F5] z-50 animate-in fade-in slide-in-from-top-2">
            <div className="flex items-start justify-between border-b border-[#DBE5F5] pb-3">
              <div className="flex items-center gap-2.5">
                <div className="w-10 h-10 rounded-full bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold text-sm">
                  {currentFacility.pic_name.substring(0, 2).toUpperCase()}
                </div>
                <div>
                  <h4 className="font-bold text-xs text-[#0B1F4B]">{currentFacility.pic_name}</h4>
                  <p className="text-[10px] text-[#52627F] capitalize">
                    {currentRole === 'district_unit'
                      ? 'District Health Officer'
                      : currentRole === 'pharmacist'
                      ? 'Certified Pharmacist'
                      : currentRole === 'facility_head'
                      ? 'Head of Facility'
                      : 'System Administrator'}
                  </p>
                </div>
              </div>
              <button
                onClick={() => setIsProfileOpen(false)}
                className="text-[#52627F] hover:text-[#0B1F4B] text-xs font-bold p-1 cursor-pointer"
              >
                ✕
              </button>
            </div>

            <div className="mt-3 space-y-2 text-xs">
              <div className="bg-[#F8FAFC] p-2.5 rounded-xl border border-[#DBE5F5]">
                <span className="text-[10px] text-[#52627F] block">Healthcare Facility:</span>
                <span className="font-bold text-[#0B1F4B]">{currentFacility.name}</span>
                <span className="text-[10px] text-[#52627F] block mt-0.5">{currentFacility.address}</span>
              </div>
              <div className="flex justify-between items-center text-[11px] pt-1">
                <span className="text-[#52627F]">Facility License:</span>
                <span className="font-mono font-bold text-[#0B1F4B]">{currentFacility.license_number}</span>
              </div>
              <div className="flex justify-between items-center text-[11px]">
                <span className="text-[#52627F]">Account Status:</span>
                <span className="px-2 py-0.5 rounded-full bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] font-bold text-[10px]">
                  Officially Verified
                </span>
              </div>
            </div>

            <div className="mt-4 pt-3 border-t border-[#DBE5F5] flex items-center justify-between">
              <button
                onClick={() => {
                  onSelectTab('admin');
                  setIsProfileOpen(false);
                }}
                className="text-xs text-[#1D4ED8] hover:underline font-bold cursor-pointer"
              >
                Account Settings →
              </button>
              <button
                onClick={() => setIsProfileOpen(false)}
                className="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-[#0B1F4B] text-xs font-semibold rounded-lg cursor-pointer"
              >
                Close
              </button>
            </div>
          </div>
        )}
      </div>
    </header>
  );
};
