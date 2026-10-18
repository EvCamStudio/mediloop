import React from 'react';
import { useApp } from '../../context/AppContext';
import { NavTab } from './Header';
import {
  Home,
  Activity,
  MapPin,
  ArrowLeftRight,
  Sparkles,
  Layers,
  TrendingUp,
  ShieldCheck,
  Building,
  User,
  X,
} from 'lucide-react';

interface SidebarProps {
  currentTab: NavTab;
  onSelectTab: (tab: NavTab) => void;
  isOpenMobile?: boolean;
  onCloseMobile?: () => void;
}

export const Sidebar: React.FC<SidebarProps> = ({
  currentTab,
  onSelectTab,
  isOpenMobile,
  onCloseMobile,
}) => {
  const { currentFacility, currentRole, transfers } = useApp();

  const pendingApprovalsCount = transfers.filter(
    (t) => t.status === 'Requested' || t.status === 'Pharmacist Approved'
  ).length;

  const menuItems: { id: NavTab; label: string; icon: React.ReactNode; badge?: number }[] = [
    { id: 'overview', label: 'Overview', icon: <Home className="w-4 h-4" /> },
    { id: 'forecast', label: 'Expiry Forecast', icon: <Activity className="w-4 h-4" /> },
    { id: 'map', label: 'Network Map', icon: <MapPin className="w-4 h-4" /> },
    {
      id: 'transfers',
      label: 'Transfers & Approvals',
      icon: <ArrowLeftRight className="w-4 h-4" />,
      badge: pendingApprovalsCount > 0 ? pendingApprovalsCount : undefined,
    },
    { id: 'matches', label: 'Match Suggestions', icon: <Sparkles className="w-4 h-4" /> },
    { id: 'inventory', label: 'Inventory Stock', icon: <Layers className="w-4 h-4" /> },
    { id: 'impact', label: 'Impact & Reports', icon: <TrendingUp className="w-4 h-4" /> },
    { id: 'admin', label: 'Settings & Admin', icon: <ShieldCheck className="w-4 h-4" /> },
  ];

  const handleItemClick = (tab: NavTab) => {
    onSelectTab(tab);
    if (onCloseMobile) onCloseMobile();
  };

  return (
    <>
      {/* Mobile Backdrop */}
      {isOpenMobile && (
        <div
          className="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 md:hidden"
          onClick={onCloseMobile}
        />
      )}

      {/* Sidebar Container (matching Image 3 Dark Navy style #0B1F4B) */}
      <aside
        className={`fixed md:sticky top-0 left-0 h-screen w-64 bg-[#0B1F4B] text-slate-300 flex flex-col justify-between z-50 transition-transform duration-300 ease-in-out shrink-0 select-none ${
          isOpenMobile ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
        }`}
      >
        <div>
          {/* Sidebar Header: Logo */}
          <div className="p-5 flex items-center justify-between border-b border-white/10">
            <button
              onClick={() => handleItemClick('landing')}
              className="flex items-center gap-2.5 text-left group cursor-pointer hover:opacity-95 transition-opacity"
              title="Back to Landing Page"
            >
              <div className="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-base shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform font-heading">
                ML
              </div>
              <div>
                <span className="text-xl font-black tracking-tight text-white block leading-none font-heading">
                  Medi<span className="text-blue-400">Loop</span>
                </span>
                <span className="text-[10px] font-semibold text-slate-400 block tracking-wider uppercase mt-1">
                  Medicine Redistribution
                </span>
              </div>
            </button>

            {/* Mobile close button */}
            <button
              onClick={onCloseMobile}
              className="p-1.5 rounded-lg text-slate-400 hover:text-white md:hidden cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Navigation Items on the Left */}
          <nav className="p-3.5 space-y-1.5">
            {menuItems.map((item) => {
              const isActive = currentTab === item.id;
              return (
                <button
                  key={item.id}
                  onClick={() => handleItemClick(item.id)}
                  className={`w-full flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-semibold transition-all cursor-pointer ${
                    isActive
                      ? 'bg-[#1D4ED8] text-white font-bold shadow-md shadow-blue-600/30'
                      : 'text-slate-300 hover:bg-white/5 hover:text-white'
                  }`}
                >
                  <div className="flex items-center gap-3">
                    <span className={isActive ? 'text-white' : 'text-slate-400'}>{item.icon}</span>
                    <span>{item.label}</span>
                  </div>
                  {item.badge !== undefined && (
                    <span className="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#D9480F] text-white">
                      {item.badge}
                    </span>
                  )}
                </button>
              );
            })}
          </nav>
        </div>

        {/* Sidebar Footer: Active Faskes & User Info */}
        <div className="p-4 border-t border-white/10 bg-[#081738]">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xs shrink-0 border border-blue-400/20">
              <Building className="w-4 h-4" />
            </div>
            <div className="min-w-0 flex-1">
              <p className="text-xs font-bold text-white truncate">{currentFacility.name}</p>
              <p className="text-[10px] text-slate-400 truncate mt-0.5">
                {currentFacility.pic_name}
              </p>
            </div>
          </div>
        </div>
      </aside>
    </>
  );
};
