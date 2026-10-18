import React, { useState } from 'react';
import { AppProvider } from './context/AppContext';
import { Header, NavTab } from './components/common/Header';
import { Sidebar } from './components/common/Sidebar';
import { Footer } from './components/common/Footer';
import { LandingPage } from './components/landing/LandingPage';
import { OverviewDashboard } from './components/dashboard/OverviewDashboard';
import { InventoryView } from './components/inventory/InventoryView';
import { ExpiryForecastView } from './components/forecast/ExpiryForecastView';
import { NetworkMapView } from './components/map/NetworkMapView';
import { MatchRecommendationsView } from './components/matches/MatchRecommendationsView';
import { TransfersView } from './components/transfers/TransfersView';
import { ImpactReportsView } from './components/impact/ImpactReportsView';
import { AdminView } from './components/admin/AdminView';
import { AboutModal } from './components/common/AboutModal';

function MainContent() {
  const [currentTab, setCurrentTab] = useState<NavTab>('landing');
  const [selectedBatchForMatch, setSelectedBatchForMatch] = useState<string | null>('batch_amx_09');
  const [isAboutOpen, setIsAboutOpen] = useState<boolean>(false);
  const [isMobileSidebarOpen, setIsMobileSidebarOpen] = useState<boolean>(false);

  const handleNavigateToMatch = (batchId: string) => {
    setSelectedBatchForMatch(batchId);
    setCurrentTab('matches');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleNavigateToMap = () => {
    setCurrentTab('map');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleNavigateToTransfers = () => {
    setCurrentTab('transfers');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleNavigateToForecast = () => {
    setCurrentTab('forecast');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // 1. Landing Page Layout (Full-width hero experience)
  if (currentTab === 'landing') {
    return (
      <div className="min-h-screen flex flex-col bg-[#F8FAFC] text-slate-800">
        <Header currentTab={currentTab} onSelectTab={setCurrentTab} />

        <main className="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <LandingPage
            onEnterApp={() => setCurrentTab('overview')}
            onOpenMatchDirectly={handleNavigateToMatch}
            onOpenAbout={() => setIsAboutOpen(true)}
          />
        </main>

        <Footer />
        <AboutModal isOpen={isAboutOpen} onClose={() => setIsAboutOpen(false)} />
      </div>
    );
  }

  // 2. Dashboard Workspace Layout (Left Sidebar + Right Content Workspace)
  return (
    <div className="min-h-screen flex bg-[#F8FAFC] text-slate-800">
      {/* Sidebar on the Left (Requested by user) */}
      <Sidebar
        currentTab={currentTab}
        onSelectTab={setCurrentTab}
        isOpenMobile={isMobileSidebarOpen}
        onCloseMobile={() => setIsMobileSidebarOpen(false)}
      />

      {/* Main Workspace Area on the Right */}
      <div className="flex-1 flex flex-col min-w-0">
        <Header
          currentTab={currentTab}
          onSelectTab={setCurrentTab}
          onToggleMobileSidebar={() => setIsMobileSidebarOpen(true)}
        />

        <main className="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6 max-w-7xl">
          {currentTab === 'overview' && (
            <OverviewDashboard
              onFindMatch={handleNavigateToMatch}
              onOpenMap={handleNavigateToMap}
              onOpenTransfers={handleNavigateToTransfers}
              onOpenForecast={handleNavigateToForecast}
            />
          )}

          {currentTab === 'inventory' && (
            <InventoryView onFindMatch={handleNavigateToMatch} />
          )}

          {currentTab === 'forecast' && (
            <ExpiryForecastView onFindMatch={handleNavigateToMatch} />
          )}

          {currentTab === 'map' && (
            <NetworkMapView
              onSelectBatchMatch={handleNavigateToMatch}
              selectedBatchId={selectedBatchForMatch}
            />
          )}

          {currentTab === 'matches' && (
            <MatchRecommendationsView
              initialBatchId={selectedBatchForMatch}
              onTransferCreated={handleNavigateToTransfers}
              onOpenMap={handleNavigateToMap}
            />
          )}

          {currentTab === 'transfers' && <TransfersView />}

          {currentTab === 'impact' && <ImpactReportsView />}

          {currentTab === 'admin' && <AdminView />}
        </main>

        <Footer />
      </div>

      <AboutModal isOpen={isAboutOpen} onClose={() => setIsAboutOpen(false)} />
    </div>
  );
}

export default function App() {
  return (
    <AppProvider>
      <MainContent />
    </AppProvider>
  );
}
