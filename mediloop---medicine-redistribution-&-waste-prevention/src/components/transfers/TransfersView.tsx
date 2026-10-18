import React, { useState, useMemo } from 'react';
import { useApp } from '../../context/AppContext';
import { TransferRequest, TransferStatus, UserRole } from '../../types';
import { SuratJalanModal } from './SuratJalanModal';
import { calculateHaversineDistance } from '../../utils/calculations';
import {
  ArrowLeftRight,
  Clock,
  CheckCircle2,
  XCircle,
  Truck,
  FileText,
  AlertCircle,
  Calendar,
  ShieldAlert,
  ChevronDown,
  ChevronUp,
  MapPin,
  Building,
  UserCheck,
  Ban,
  PackageCheck,
} from 'lucide-react';

export const TransfersView: React.FC = () => {
  const {
    transfers,
    facilities,
    drugs,
    batches,
    currentRole,
    currentFacility,
    switchRole,
    updateTransferStatus,
  } = useApp();

  const [filterTab, setFilterTab] = useState<'needs_action' | 'all' | 'in_progress' | 'completed' | 'rejected'>('needs_action');
  const [selectedTransferForDoc, setSelectedTransferForDoc] = useState<TransferRequest | null>(null);
  
  // Rejection modal state
  const [rejectModalTransfer, setRejectModalTransfer] = useState<TransferRequest | null>(null);
  const [rejectionReason, setRejectionReason] = useState<string>('');

  // Handover modal state
  const [handoverModalTransfer, setHandoverModalTransfer] = useState<TransferRequest | null>(null);
  const [courierName, setCourierName] = useState<string>('District Health Logistics (Rizal)');
  const [courierPlate, setCourierPlate] = useState<string>('BK 4912 KD');

  // Receiver receipt verification modal state
  const [receiptModalTransfer, setReceiptModalTransfer] = useState<TransferRequest | null>(null);
  const [receivedQty, setReceivedQty] = useState<number>(0);
  const [verifiedBatchCheck, setVerifiedBatchCheck] = useState<boolean>(true);
  const [verifiedExpiryCheck, setVerifiedExpiryCheck] = useState<boolean>(true);

  // Cancellation modal state
  const [cancelModalTransfer, setCancelModalTransfer] = useState<TransferRequest | null>(null);
  const [cancelReason, setCancelReason] = useState<string>('Surplus batch reclaimed for local emergency buffer');

  // Expanded audit trails map
  const [expandedHistory, setExpandedHistory] = useState<Record<string, boolean>>({});

  const toggleHistory = (transferId: string) => {
    setExpandedHistory((prev) => ({ ...prev, [transferId]: !prev[transferId] }));
  };

  // Helper to determine if a transfer needs the active user's action
  const needsAction = (t: TransferRequest, role: UserRole): boolean => {
    if (t.status === 'Completed' || t.status === 'Rejected' || t.status === 'Cancelled') {
      return false;
    }
    if (role === 'pharmacist' || role === 'facility_head') {
      return t.status === 'Requested' || t.status === 'District Approved';
    }
    if (role === 'district_unit' || role === 'admin') {
      return t.status === 'Pharmacist Approved' || t.status === 'Requested';
    }
    if (role === 'receiver') {
      return t.status === 'Handed Over';
    }
    return false;
  };

  // Filter tabs counts
  const tabCounts = useMemo(() => {
    let needsActionCount = 0;
    let inProgressCount = 0;
    let completedCount = 0;
    let rejectedCount = 0;

    for (const t of transfers) {
      if (needsAction(t, currentRole)) needsActionCount++;
      if (!['Completed', 'Rejected', 'Cancelled'].includes(t.status)) inProgressCount++;
      if (t.status === 'Completed') completedCount++;
      if (t.status === 'Rejected' || t.status === 'Cancelled') rejectedCount++;
    }

    return {
      all: transfers.length,
      needs_action: needsActionCount,
      in_progress: inProgressCount,
      completed: completedCount,
      rejected: rejectedCount,
    };
  }, [transfers, currentRole]);

  // Filtered & Sorted Transfers: Action-needed items FIRST, then newest first
  const sortedAndFilteredTransfers = useMemo(() => {
    return transfers
      .filter((t) => {
        if (filterTab === 'needs_action') return needsAction(t, currentRole);
        if (filterTab === 'in_progress') return !['Completed', 'Rejected', 'Cancelled'].includes(t.status);
        if (filterTab === 'completed') return t.status === 'Completed';
        if (filterTab === 'rejected') return t.status === 'Rejected' || t.status === 'Cancelled';
        return true;
      })
      .sort((a, b) => {
        const aAction = needsAction(a, currentRole) ? 1 : 0;
        const bAction = needsAction(b, currentRole) ? 1 : 0;
        if (aAction !== bAction) return bAction - aAction;
        return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
      });
  }, [transfers, filterTab, currentRole]);

  // Stepper steps
  const stepperSteps: { key: TransferStatus; label: string }[] = [
    { key: 'Requested', label: 'Requested' },
    { key: 'Pharmacist Approved', label: 'Pharmacist Approved' },
    { key: 'District Approved', label: 'District Approved' },
    { key: 'Handed Over', label: 'Handed Over' },
    { key: 'Completed', label: 'Completed' },
  ];

  const getStepStatus = (
    stepKey: TransferStatus,
    currentStatus: TransferStatus
  ): 'completed' | 'current' | 'future' | 'rejected' | 'cancelled' => {
    const order: TransferStatus[] = [
      'Requested',
      'Pharmacist Approved',
      'District Approved',
      'Handed Over',
      'Completed',
    ];
    const stepIdx = order.indexOf(stepKey);

    if (currentStatus === 'Rejected') {
      // Rejection happened at District Approved step for demo transfer
      if (stepIdx < 2) return 'completed';
      if (stepIdx === 2) return 'rejected';
      return 'future';
    }

    if (currentStatus === 'Cancelled') {
      if (stepIdx === 0) return 'cancelled';
      return 'future';
    }

    const currentIdx = order.indexOf(currentStatus);
    if (stepIdx < currentIdx) return 'completed';
    if (stepIdx === currentIdx) return 'current';
    return 'future';
  };

  // Find historical actor & time for a step
  const getStepEvent = (t: TransferRequest, stepKey: TransferStatus) => {
    return t.history.find((ev) => {
      if (stepKey === 'Requested') {
        return (
          ev.action.toLowerCase().includes('initiated') ||
          ev.action.toLowerCase().includes('drafted') ||
          ev.action.toLowerCase().includes('created')
        );
      }
      if (stepKey === 'Pharmacist Approved') {
        return (
          ev.action.toLowerCase().includes('pharmacist verified') ||
          ev.action.toLowerCase().includes('pharmacist approved')
        );
      }
      if (stepKey === 'District Approved') {
        return (
          ev.action.toLowerCase().includes('district approved') ||
          ev.action.toLowerCase().includes('approved by district')
        );
      }
      if (stepKey === 'Handed Over') {
        return (
          ev.action.toLowerCase().includes('handed over') ||
          ev.action.toLowerCase().includes('dispatched')
        );
      }
      if (stepKey === 'Completed') {
        return (
          ev.action.toLowerCase().includes('receipt confirmed') ||
          ev.action.toLowerCase().includes('completed')
        );
      }
      return false;
    });
  };

  const handleOpenRejectModal = (t: TransferRequest) => {
    setRejectModalTransfer(t);
    setRejectionReason('');
  };

  const handleConfirmReject = () => {
    if (!rejectModalTransfer || !rejectionReason.trim()) return;
    updateTransferStatus(
      rejectModalTransfer.id,
      'Rejected',
      'Rejected by District Pharmacy Unit',
      rejectionReason.trim()
    );
    setRejectModalTransfer(null);
  };

  const handleOpenReceiptModal = (t: TransferRequest) => {
    setReceiptModalTransfer(t);
    setReceivedQty(t.qty);
    setVerifiedBatchCheck(true);
    setVerifiedExpiryCheck(true);
  };

  const handleConfirmReceipt = () => {
    if (!receiptModalTransfer || !verifiedBatchCheck || !verifiedExpiryCheck) return;
    updateTransferStatus(
      receiptModalTransfer.id,
      'Completed',
      `Physical medicine received (${receivedQty} boxes). Batch and expiry verified by receiving facility.`
    );
    setReceiptModalTransfer(null);
  };

  const handleOpenHandoverModal = (t: TransferRequest) => {
    setHandoverModalTransfer(t);
  };

  const handleConfirmHandover = () => {
    if (!handoverModalTransfer) return;
    updateTransferStatus(
      handoverModalTransfer.id,
      'Handed Over',
      `Handed over to courier ${courierName} (${courierPlate}) with Batch Document`,
      undefined,
      { name: courierName, plate: courierPlate }
    );
    setHandoverModalTransfer(null);
  };

  const handleOpenCancelModal = (t: TransferRequest) => {
    setCancelModalTransfer(t);
  };

  const handleConfirmCancel = () => {
    if (!cancelModalTransfer) return;
    updateTransferStatus(
      cancelModalTransfer.id,
      'Cancelled',
      `Transfer cancelled by sender: ${cancelReason}`
    );
    setCancelModalTransfer(null);
  };

  return (
    <div className="space-y-6 font-body text-slate-800">
      {/* 1. Calm Regulatory Notice Banner */}
      <div className="bg-[#EAF1FF] border border-[#DBE5F5] p-4 rounded-2xl flex items-start gap-3.5">
        <AlertCircle className="w-5 h-5 text-[#1D4ED8] shrink-0 mt-0.5" />
        <p className="text-xs text-[#1D4ED8] leading-relaxed font-medium">
          <span className="font-bold text-[#0B1F4B]">Regulatory Notice:</span> MediLoop provides recommendations only.
          Every transfer must follow local regulations and be approved through the official district pharmacy channel.
          Stock balances change only when the receiving facility confirms receipt.
        </p>
      </div>

      {/* 2. Page Header with Title, Role Switcher, and Filter Tabs */}
      <div className="bg-white p-6 rounded-2xl border border-[#DBE5F5] shadow-xs space-y-5">
        <div className="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h2 className="text-xl font-extrabold text-[#0B1F4B]">
              Inter-Facility Medicine Transfers
            </h2>
            <p className="text-xs text-[#52627F] mt-1">
              Review, verify, and monitor active and completed medicine redistribution requests across the district network.
            </p>
          </div>

          {/* Demo Role Switcher */}
          <div className="flex items-center gap-2 bg-[#F8FAFC] border border-[#DBE5F5] p-2 rounded-xl text-xs">
            <UserCheck className="w-4 h-4 text-[#1D4ED8]" />
            <span className="font-bold text-[#0B1F4B]">Demo Role Switcher:</span>
            <select
              value={currentRole === 'district_unit' ? 'district_unit' : currentRole === 'receiver' ? 'receiver' : 'pharmacist'}
              onChange={(e) => switchRole(e.target.value as UserRole)}
              className="bg-white border border-[#DBE5F5] rounded-lg px-2.5 py-1 text-xs font-semibold text-[#0B1F4B] outline-none cursor-pointer focus:ring-2 focus:ring-blue-500"
            >
              <option value="pharmacist">Pharmacist (Puskesmas Simpang Limun - Sender)</option>
              <option value="district_unit">District Unit (Medan Health Department)</option>
              <option value="receiver">Receiver (Klinik Pratama Sentosa)</option>
            </select>
          </div>
        </div>

        {/* Filter Tabs with Counts (Default: Needs My Action) */}
        <div className="flex flex-wrap items-center gap-2 border-t border-[#DBE5F5] pt-4">
          <button
            onClick={() => setFilterTab('needs_action')}
            className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              filterTab === 'needs_action'
                ? 'bg-[#1D4ED8] text-white shadow-xs'
                : 'bg-[#F8FAFC] text-[#52627F] hover:text-[#0B1F4B] border border-[#DBE5F5]'
            }`}
          >
            <span>Needs My Action</span>
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] font-extrabold ${
                filterTab === 'needs_action'
                  ? 'bg-white text-[#1D4ED8]'
                  : 'bg-[#EAF1FF] text-[#1D4ED8]'
              }`}
            >
              {tabCounts.needs_action}
            </span>
          </button>

          <button
            onClick={() => setFilterTab('all')}
            className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              filterTab === 'all'
                ? 'bg-[#1D4ED8] text-white shadow-xs'
                : 'bg-[#F8FAFC] text-[#52627F] hover:text-[#0B1F4B] border border-[#DBE5F5]'
            }`}
          >
            <span>All Transfers</span>
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] font-extrabold ${
                filterTab === 'all' ? 'bg-white text-[#1D4ED8]' : 'bg-[#EAF1FF] text-[#1D4ED8]'
              }`}
            >
              {tabCounts.all}
            </span>
          </button>

          <button
            onClick={() => setFilterTab('in_progress')}
            className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              filterTab === 'in_progress'
                ? 'bg-[#1D4ED8] text-white shadow-xs'
                : 'bg-[#F8FAFC] text-[#52627F] hover:text-[#0B1F4B] border border-[#DBE5F5]'
            }`}
          >
            <span>In Progress</span>
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] font-extrabold ${
                filterTab === 'in_progress' ? 'bg-white text-[#1D4ED8]' : 'bg-[#EAF1FF] text-[#1D4ED8]'
              }`}
            >
              {tabCounts.in_progress}
            </span>
          </button>

          <button
            onClick={() => setFilterTab('completed')}
            className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              filterTab === 'completed'
                ? 'bg-[#1D4ED8] text-white shadow-xs'
                : 'bg-[#F8FAFC] text-[#52627F] hover:text-[#0B1F4B] border border-[#DBE5F5]'
            }`}
          >
            <span>Completed</span>
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] font-extrabold ${
                filterTab === 'completed' ? 'bg-white text-[#1D4ED8]' : 'bg-[#EAF1FF] text-[#1D4ED8]'
              }`}
            >
              {tabCounts.completed}
            </span>
          </button>

          <button
            onClick={() => setFilterTab('rejected')}
            className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              filterTab === 'rejected'
                ? 'bg-[#1D4ED8] text-white shadow-xs'
                : 'bg-[#F8FAFC] text-[#52627F] hover:text-[#0B1F4B] border border-[#DBE5F5]'
            }`}
          >
            <span>Rejected / Cancelled</span>
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] font-extrabold ${
                filterTab === 'rejected' ? 'bg-white text-[#1D4ED8]' : 'bg-[#EAF1FF] text-[#1D4ED8]'
              }`}
            >
              {tabCounts.rejected}
            </span>
          </button>
        </div>
      </div>

      {/* 3. Transfer Cards List */}
      <div className="space-y-4">
        {sortedAndFilteredTransfers.map((t) => {
          const sender = facilities.find((f) => f.id === t.from_facility_id);
          const receiver = facilities.find((f) => f.id === t.to_facility_id);
          const drug = drugs.find((d) => d.id === t.drug_id);
          const batch = batches.find((b) => b.id === t.batch_id);

          // Calculate distance (km)
          const distanceKm =
            sender && receiver
              ? calculateHaversineDistance(sender.lat, sender.lng, receiver.lat, receiver.lng)
              : 4.8;

          // Days to expiry check: Risk orange ONLY if within 60 days
          const daysToExpiry = batch
            ? Math.ceil((new Date(batch.expiry_date).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24))
            : 45;
          const isNearExpiry = daysToExpiry <= 60;

          // Check if batch document is visible (ONLY District Approved or later)
          const showBatchDocument = ['District Approved', 'Handed Over', 'Completed'].includes(t.status);

          // Status Badge styling
          let statusBadge = (
            <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
              {t.status}
            </span>
          );
          if (t.status === 'Rejected') {
            statusBadge = (
              <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white text-[#D9480F] border border-[#D9480F]/40 flex items-center gap-1">
                <XCircle className="w-3.5 h-3.5 text-[#D9480F]" />
                <span>Rejected</span>
              </span>
            );
          } else if (t.status === 'Cancelled') {
            statusBadge = (
              <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white text-[#D9480F] border border-[#D9480F]/40 flex items-center gap-1">
                <Ban className="w-3.5 h-3.5 text-[#D9480F]" />
                <span>Cancelled</span>
              </span>
            );
          } else if (t.status === 'Completed') {
            statusBadge = (
              <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] flex items-center gap-1">
                <CheckCircle2 className="w-3.5 h-3.5 text-[#1D4ED8]" />
                <span>Completed</span>
              </span>
            );
          }

          const isActionItem = needsAction(t, currentRole);

          return (
            <div
              key={t.id}
              className={`bg-white rounded-2xl border transition-all p-5 shadow-xs space-y-4 ${
                isActionItem
                  ? 'border-[#1D4ED8] ring-2 ring-blue-500/20'
                  : 'border-[#DBE5F5]'
              }`}
            >
              {/* Card Top Meta */}
              <div className="flex flex-wrap items-center justify-between gap-3 border-b border-[#DBE5F5] pb-3">
                <div className="flex flex-wrap items-center gap-3">
                  <span className="font-mono text-xs font-extrabold text-[#0B1F4B] bg-[#EAF1FF] px-2.5 py-1 rounded-md border border-[#DBE5F5]">
                    {t.transfer_code}
                  </span>
                  {statusBadge}
                  <span className="text-xs text-[#52627F] flex items-center gap-1 font-medium">
                    <Calendar className="w-3.5 h-3.5 text-[#52627F]" />
                    {new Date(t.created_at).toLocaleDateString('en-US', {
                      day: 'numeric',
                      month: 'short',
                      year: 'numeric',
                    })}
                  </span>
                  {isActionItem && (
                    <span className="px-2 py-0.5 rounded text-[11px] font-extrabold bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
                      Action Required
                    </span>
                  )}
                </div>

                <div className="flex items-center gap-2">
                  {/* Batch Document button: ONLY District Approved or later */}
                  {showBatchDocument && (
                    <button
                      onClick={() => setSelectedTransferForDoc(t)}
                      className="px-3 py-1.5 text-xs font-semibold border border-[#DBE5F5] text-[#0B1F4B] bg-[#F8FAFC] hover:bg-[#EAF1FF] rounded-lg flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                      <FileText className="w-3.5 h-3.5 text-[#1D4ED8]" />
                      <span>Batch Document</span>
                    </button>
                  )}
                </div>
              </div>

              {/* Card Content Grid */}
              <div className="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
                {/* Drug Details (col 4) */}
                <div className="lg:col-span-4 space-y-1">
                  <span className="text-[10px] font-bold text-[#52627F] uppercase tracking-wider block">
                    Medicine
                  </span>
                  <h4 className="font-bold text-sm text-[#0B1F4B]">{drug?.name}</h4>
                  <p className="text-xs text-[#52627F]">
                    Batch: <span className="font-mono font-bold text-[#0B1F4B]">#{batch?.batch_no}</span> • Expiry:{' '}
                    <span className={`font-bold ${isNearExpiry ? 'text-[#D9480F]' : 'text-[#0B1F4B]'}`}>
                      {batch?.expiry_date} ({daysToExpiry} days)
                    </span>
                  </p>
                  <p className="text-xs font-bold text-[#1D4ED8]">
                    Quantity: {t.qty} boxes
                  </p>
                </div>

                {/* Route Visual & Match Details (col 5) */}
                <div className="lg:col-span-5 bg-[#F8FAFC] p-3.5 rounded-xl border border-[#DBE5F5] text-xs space-y-2">
                  <div className="flex items-center justify-between">
                    <div>
                      <span className="text-[10px] text-[#52627F] block">Origin (Sender):</span>
                      <span className="font-bold text-[#0B1F4B]">{sender?.name}</span>
                    </div>
                    <div className="text-center px-2">
                      <ArrowLeftRight className="w-4 h-4 text-[#1D4ED8] mx-auto" />
                      <span className="text-[11px] font-bold text-[#1D4ED8] block mt-0.5">
                        {distanceKm} km
                      </span>
                    </div>
                    <div className="text-right">
                      <span className="text-[10px] text-[#52627F] block">Destination (Receiver):</span>
                      <span className="font-bold text-[#0B1F4B]">{receiver?.name}</span>
                    </div>
                  </div>

                  {/* Match Reason */}
                  {t.notes && (
                    <div className="pt-2 border-t border-[#DBE5F5] text-[11px] text-[#52627F] leading-tight">
                      <span className="font-semibold text-[#0B1F4B]">{t.notes}</span>
                    </div>
                  )}
                </div>

                {/* Right Action Area: Whose turn is it? (col 3) */}
                <div className="lg:col-span-3 flex flex-col items-start lg:items-end justify-center space-y-2">
                  {/* Status: Requested */}
                  {t.status === 'Requested' && (
                    <div className="space-y-1.5 w-full text-right">
                      {(currentRole === 'pharmacist' || currentRole === 'facility_head' || currentRole === 'district_unit' || currentRole === 'admin') && (
                        <button
                          onClick={() =>
                            updateTransferStatus(
                              t.id,
                              'Pharmacist Approved',
                              'Verified by facility pharmacist & submitted to district'
                            )
                          }
                          className="w-full lg:w-auto px-4 py-2 bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer"
                        >
                          Verify & Submit to District
                        </button>
                      )}
                      <div>
                        <button
                          onClick={() => handleOpenCancelModal(t)}
                          className="px-3 py-1.5 text-xs font-semibold text-[#52627F] hover:text-[#D9480F] border border-[#DBE5F5] hover:border-[#D9480F]/40 rounded-lg transition-colors cursor-pointer"
                        >
                          Cancel
                        </button>
                      </div>
                    </div>
                  )}

                  {/* Status: Pharmacist Approved */}
                  {t.status === 'Pharmacist Approved' && (
                    <div className="space-y-2 w-full text-right">
                      {currentRole === 'district_unit' || currentRole === 'admin' ? (
                        <div className="flex flex-col sm:flex-row items-end justify-end gap-2">
                          <button
                            onClick={() =>
                              updateTransferStatus(
                                t.id,
                                'District Approved',
                                'Official redistribution approved by District Health Pharmacy Unit'
                              )
                            }
                            className="px-4 py-2 bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer"
                          >
                            Approve
                          </button>
                          <button
                            onClick={() => handleOpenRejectModal(t)}
                            className="px-3 py-2 bg-[#F8FAFC] text-[#D9480F] border border-[#D9480F]/40 text-xs font-bold rounded-xl hover:bg-orange-50 transition-colors cursor-pointer"
                          >
                            Reject
                          </button>
                        </div>
                      ) : (
                        <div className="space-y-1.5">
                          <p className="text-xs text-[#52627F] italic font-medium">
                            Waiting for District Pharmacy Unit
                          </p>
                          <button
                            onClick={() => handleOpenCancelModal(t)}
                            className="px-3 py-1 text-xs font-semibold text-[#52627F] hover:text-[#D9480F] border border-[#DBE5F5] rounded-lg transition-colors cursor-pointer"
                          >
                            Cancel
                          </button>
                        </div>
                      )}
                    </div>
                  )}

                  {/* Status: District Approved */}
                  {t.status === 'District Approved' && (
                    <div className="space-y-1.5 w-full text-right">
                      <button
                        onClick={() => handleOpenHandoverModal(t)}
                        className="w-full lg:w-auto px-4 py-2 bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer"
                      >
                        <Truck className="w-3.5 h-3.5" />
                        <span>Mark as Handed Over</span>
                      </button>
                      <div>
                        <button
                          onClick={() => handleOpenCancelModal(t)}
                          className="px-3 py-1 text-xs font-semibold text-[#52627F] hover:text-[#D9480F] border border-[#DBE5F5] rounded-lg transition-colors cursor-pointer"
                        >
                          Cancel
                        </button>
                      </div>
                    </div>
                  )}

                  {/* Status: Handed Over */}
                  {t.status === 'Handed Over' && (
                    <div className="space-y-1.5 w-full text-right">
                      {currentRole === 'receiver' || currentRole === 'district_unit' || currentRole === 'admin' ? (
                        <button
                          onClick={() => handleOpenReceiptModal(t)}
                          className="w-full lg:w-auto px-4 py-2 bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer"
                        >
                          <PackageCheck className="w-3.5 h-3.5" />
                          <span>Confirm Receipt</span>
                        </button>
                      ) : (
                        <p className="text-xs text-[#52627F] italic font-medium">
                          Waiting for receiver confirmation
                        </p>
                      )}
                    </div>
                  )}

                  {/* Status: Completed */}
                  {t.status === 'Completed' && (
                    <div className="inline-flex items-center gap-1.5 text-xs font-bold text-[#1D4ED8] bg-[#EAF1FF] border border-[#DBE5F5] px-3 py-1.5 rounded-xl">
                      <CheckCircle2 className="w-4 h-4 text-[#1D4ED8]" />
                      <span>Completed - stock balances updated</span>
                    </div>
                  )}

                  {/* Status: Rejected or Cancelled */}
                  {(t.status === 'Rejected' || t.status === 'Cancelled') && (
                    <div className="text-right text-xs text-[#D9480F] space-y-0.5">
                      <span className="font-bold flex items-center justify-end gap-1">
                        <XCircle className="w-3.5 h-3.5 text-[#D9480F]" />
                        <span>{t.status}</span>
                      </span>
                      {t.rejection_reason && (
                        <p className="text-[11px] text-[#52627F] max-w-[200px]">
                          Reason: {t.rejection_reason}
                        </p>
                      )}
                    </div>
                  )}
                </div>
              </div>

              {/* 5-Step Progress Stepper (Labels minimum 13px, fixed-height aligned) */}
              <div className="pt-4 border-t border-[#DBE5F5]">
                <div className="relative">
                  {/* Background Track Line */}
                  <div className="absolute top-4 left-6 right-6 h-0.5 bg-[#DBE5F5] z-0" />

                  {/* Stepper Columns */}
                  <div className="grid grid-cols-5 relative z-10">
                    {stepperSteps.map((step, idx) => {
                      const state = getStepStatus(step.key, t.status);
                      const event = getStepEvent(t, step.key);

                      let circleStyle = 'bg-white border-[#DBE5F5] text-[#52627F]';
                      let circleContent: React.ReactNode = idx + 1;

                      if (state === 'completed') {
                        circleStyle = 'bg-[#1D4ED8] border-[#1D4ED8] text-white';
                        circleContent = '✓';
                      } else if (state === 'current') {
                        circleStyle = 'bg-[#1D4ED8] border-[#1D4ED8] text-white ring-4 ring-blue-100';
                        circleContent = idx + 1;
                      } else if (state === 'rejected' || state === 'cancelled') {
                        circleStyle = 'bg-[#D9480F] border-[#D9480F] text-white';
                        circleContent = '✕';
                      }

                      return (
                        <div key={step.key} className="flex flex-col items-center text-center">
                          {/* Step Circle */}
                          <div
                            className={`w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-xs transition-all ${circleStyle}`}
                          >
                            {circleContent}
                          </div>

                          {/* Fixed-Height Label & Meta Container (Guarantees horizontal circle alignment) */}
                          <div className="mt-2 min-h-[52px] flex flex-col justify-start items-center max-w-[140px]">
                            <span
                              className={`text-[13px] leading-tight ${
                                state === 'current'
                                  ? 'font-extrabold text-[#0B1F4B]'
                                  : state === 'completed'
                                  ? 'font-bold text-[#0B1F4B]'
                                  : state === 'rejected' || state === 'cancelled'
                                  ? 'font-bold text-[#D9480F]'
                                  : 'font-medium text-[#52627F]'
                              }`}
                            >
                              {step.label}
                            </span>

                            {/* Actor & Timestamp */}
                            {event ? (
                              <span className="text-[11px] text-[#52627F] mt-1 leading-tight block">
                                {event.actor_name},{' '}
                                {new Date(event.timestamp).toLocaleDateString('en-US', {
                                  month: 'short',
                                  day: 'numeric',
                                })}
                              </span>
                            ) : state === 'rejected' ? (
                              <span className="text-[11px] text-[#D9480F] mt-1 font-semibold block">
                                Rejected
                              </span>
                            ) : state === 'cancelled' ? (
                              <span className="text-[11px] text-[#D9480F] mt-1 font-semibold block">
                                Cancelled
                              </span>
                            ) : null}
                          </div>
                        </div>
                      );
                    })}
                  </div>
                </div>

                {/* Rejection / Cancellation callout note if terminal state */}
                {t.status === 'Rejected' && t.rejection_reason && (
                  <div className="mt-3 p-3 bg-orange-50 border border-[#D9480F]/30 rounded-xl text-xs text-[#D9480F] flex items-center gap-2">
                    <AlertCircle className="w-4 h-4 shrink-0 text-[#D9480F]" />
                    <span>
                      <b>Rejection Reason:</b> {t.rejection_reason}
                    </span>
                  </div>
                )}
              </div>

              {/* Card Audit Trail Expander ("History") */}
              <div className="pt-2 border-t border-[#DBE5F5]/60">
                <button
                  onClick={() => toggleHistory(t.id)}
                  className="flex items-center gap-1.5 text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] transition-colors cursor-pointer"
                >
                  <Clock className="w-3.5 h-3.5 text-[#1D4ED8]" />
                  <span>Audit Trail & History ({t.history.length} events)</span>
                  {expandedHistory[t.id] ? (
                    <ChevronUp className="w-3.5 h-3.5 text-[#52627F]" />
                  ) : (
                    <ChevronDown className="w-3.5 h-3.5 text-[#52627F]" />
                  )}
                </button>

                {expandedHistory[t.id] && (
                  <div className="mt-3 bg-[#F8FAFC] border border-[#DBE5F5] rounded-xl p-3.5 space-y-2.5 text-xs">
                    {t.history.map((ev, i) => (
                      <div
                        key={ev.id || i}
                        className="flex flex-wrap items-start justify-between gap-2 border-b border-[#DBE5F5]/60 pb-2 last:border-b-0 last:pb-0"
                      >
                        <div className="space-y-0.5">
                          <p className="font-bold text-[#0B1F4B]">{ev.action}</p>
                          <p className="text-[11px] text-[#52627F]">
                            By <span className="font-semibold text-[#0B1F4B]">{ev.actor_name}</span> ({ev.actor_role})
                          </p>
                          {ev.note && (
                            <p className="text-[11px] text-[#52627F] italic">"{ev.note}"</p>
                          )}
                        </div>
                        <span className="text-[11px] font-mono text-[#52627F]">
                          {new Date(ev.timestamp).toLocaleString('en-US', {
                            dateStyle: 'medium',
                            timeStyle: 'short',
                          })}
                        </span>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>
          );
        })}

        {sortedAndFilteredTransfers.length === 0 && (
          <div className="p-12 bg-white rounded-2xl border border-[#DBE5F5] text-center text-[#52627F] space-y-2">
            <ArrowLeftRight className="w-10 h-10 mx-auto text-[#52627F]/40" />
            <h4 className="font-bold text-sm text-[#0B1F4B]">No transfers in this view</h4>
            <p className="text-xs">
              {filterTab === 'needs_action'
                ? 'All transfers are currently up to date for your role.'
                : 'No transfer records match the selected tab filter.'}
            </p>
          </div>
        )}
      </div>

      {/* Reject Modal with REQUIRED reason (US-05) */}
      {rejectModalTransfer && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-[#DBE5F5] space-y-4">
            <div className="flex items-start gap-3">
              <div className="w-10 h-10 rounded-full bg-[#EAF1FF] text-[#D9480F] flex items-center justify-center shrink-0">
                <ShieldAlert className="w-5 h-5 text-[#D9480F]" />
              </div>
              <div>
                <h3 className="font-bold text-sm text-[#0B1F4B]">
                  Decline Redistribution Request
                </h3>
                <p className="text-xs text-[#52627F] mt-0.5">
                  A mandatory formal reason is required for compliance and regulatory audit logs.
                </p>
              </div>
            </div>

            <div>
              <label className="block text-xs font-bold text-[#0B1F4B] mb-1">
                Reason for Rejection <span className="text-[#D9480F]">*</span>:
              </label>
              <textarea
                value={rejectionReason}
                onChange={(e) => setRejectionReason(e.target.value)}
                placeholder="e.g. Receiver shelf-life requirement not met / Central supply scheduled for arrival..."
                rows={3}
                className="w-full text-xs p-3 border border-[#DBE5F5] rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-[#0B1F4B]"
              />
              {!rejectionReason.trim() && (
                <p className="text-[11px] text-[#D9480F] mt-1 font-semibold">
                  Reason is required before submitting rejection.
                </p>
              )}
            </div>

            <div className="flex items-center justify-end gap-2 pt-2">
              <button
                onClick={() => setRejectModalTransfer(null)}
                className="px-4 py-2 text-xs font-semibold text-[#52627F] hover:bg-[#F8FAFC] rounded-xl cursor-pointer"
              >
                Cancel
              </button>
              <button
                onClick={handleConfirmReject}
                disabled={!rejectionReason.trim()}
                className="px-4 py-2 text-xs font-bold text-white bg-[#D9480F] hover:bg-orange-700 disabled:opacity-40 rounded-xl shadow-xs cursor-pointer"
              >
                Confirm Rejection
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Handover Modal */}
      {handoverModalTransfer && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-[#DBE5F5] space-y-4">
            <div className="flex items-start gap-3">
              <div className="w-10 h-10 rounded-full bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center shrink-0">
                <Truck className="w-5 h-5 text-[#1D4ED8]" />
              </div>
              <div>
                <h3 className="font-bold text-sm text-[#0B1F4B]">
                  Mark as Handed Over to Fleet
                </h3>
                <p className="text-xs text-[#52627F] mt-0.5">
                  Record official courier details and handover documentation.
                </p>
              </div>
            </div>

            <div className="space-y-3 text-xs">
              <div>
                <label className="block font-semibold text-[#0B1F4B] mb-1">
                  Courier / Logistics Officer Name:
                </label>
                <input
                  type="text"
                  value={courierName}
                  onChange={(e) => setCourierName(e.target.value)}
                  className="w-full p-2.5 border border-[#DBE5F5] rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-[#0B1F4B]"
                />
              </div>
              <div>
                <label className="block font-semibold text-[#0B1F4B] mb-1">
                  Vehicle License Plate:
                </label>
                <input
                  type="text"
                  value={courierPlate}
                  onChange={(e) => setCourierPlate(e.target.value)}
                  className="w-full p-2.5 border border-[#DBE5F5] rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-mono text-[#0B1F4B]"
                />
              </div>
            </div>

            <div className="flex items-center justify-end gap-2 pt-2">
              <button
                onClick={() => setHandoverModalTransfer(null)}
                className="px-4 py-2 text-xs font-semibold text-[#52627F] hover:bg-[#F8FAFC] rounded-xl cursor-pointer"
              >
                Cancel
              </button>
              <button
                onClick={handleConfirmHandover}
                className="px-4 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-xl shadow-xs cursor-pointer"
              >
                Confirm Handover
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Receiver Confirmation Modal (US-06 verification fields) */}
      {receiptModalTransfer && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-[#DBE5F5] space-y-4">
            <div className="flex items-start gap-3">
              <div className="w-10 h-10 rounded-full bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center shrink-0">
                <PackageCheck className="w-5 h-5 text-[#1D4ED8]" />
              </div>
              <div>
                <h3 className="font-bold text-sm text-[#0B1F4B]">
                  Confirm Receipt of Medicine
                </h3>
                <p className="text-xs text-[#52627F] mt-0.5">
                  Verify physical quantities and batch details before updating facility stock balances.
                </p>
              </div>
            </div>

            <div className="space-y-3 text-xs">
              <div className="p-3 bg-[#F8FAFC] border border-[#DBE5F5] rounded-xl space-y-1">
                <p className="font-bold text-[#0B1F4B]">{drugs.find((d) => d.id === receiptModalTransfer.drug_id)?.name}</p>
                <p className="text-[#52627F]">
                  Documented Batch: <span className="font-mono font-bold text-[#0B1F4B]">
                    #{batches.find((b) => b.id === receiptModalTransfer.batch_id)?.batch_no}
                  </span>
                </p>
              </div>

              <div>
                <label className="block font-semibold text-[#0B1F4B] mb-1">
                  Quantity Received (boxes):
                </label>
                <input
                  type="number"
                  min="1"
                  value={receivedQty}
                  onChange={(e) => setReceivedQty(parseInt(e.target.value, 10) || 0)}
                  className="w-full p-2.5 border border-[#DBE5F5] rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-bold text-[#0B1F4B]"
                />
              </div>

              <div className="space-y-2 pt-2 border-t border-[#DBE5F5]">
                <label className="flex items-center gap-2 cursor-pointer text-[#0B1F4B] font-medium">
                  <input
                    type="checkbox"
                    checked={verifiedBatchCheck}
                    onChange={(e) => setVerifiedBatchCheck(e.target.checked)}
                    className="w-4 h-4 text-[#1D4ED8] rounded border-gray-300 focus:ring-blue-500"
                  />
                  <span>Physical batch number verified and matches documentation</span>
                </label>

                <label className="flex items-center gap-2 cursor-pointer text-[#0B1F4B] font-medium">
                  <input
                    type="checkbox"
                    checked={verifiedExpiryCheck}
                    onChange={(e) => setVerifiedExpiryCheck(e.target.checked)}
                    className="w-4 h-4 text-[#1D4ED8] rounded border-gray-300 focus:ring-blue-500"
                  />
                  <span>Physical expiry date inspected and packaging intact</span>
                </label>
              </div>
            </div>

            <div className="flex items-center justify-end gap-2 pt-2">
              <button
                onClick={() => setReceiptModalTransfer(null)}
                className="px-4 py-2 text-xs font-semibold text-[#52627F] hover:bg-[#F8FAFC] rounded-xl cursor-pointer"
              >
                Cancel
              </button>
              <button
                onClick={handleConfirmReceipt}
                disabled={receivedQty <= 0 || !verifiedBatchCheck || !verifiedExpiryCheck}
                className="px-4 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 disabled:opacity-40 rounded-xl shadow-xs cursor-pointer"
              >
                Confirm Receipt & Update Stock
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Cancellation Confirmation Modal */}
      {cancelModalTransfer && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-[#DBE5F5] space-y-4">
            <div className="flex items-start gap-3">
              <div className="w-10 h-10 rounded-full bg-[#EAF1FF] text-[#D9480F] flex items-center justify-center shrink-0">
                <Ban className="w-5 h-5 text-[#D9480F]" />
              </div>
              <div>
                <h3 className="font-bold text-sm text-[#0B1F4B]">
                  Cancel Transfer Request?
                </h3>
                <p className="text-xs text-[#52627F] mt-0.5">
                  Are you sure you want to cancel transfer <span className="font-mono font-bold text-[#0B1F4B]">{cancelModalTransfer.transfer_code}</span>?
                  This action will terminate the redistribution workflow.
                </p>
              </div>
            </div>

            <div>
              <label className="block text-xs font-semibold text-[#0B1F4B] mb-1">
                Reason for cancellation:
              </label>
              <input
                type="text"
                value={cancelReason}
                onChange={(e) => setCancelReason(e.target.value)}
                className="w-full p-2.5 text-xs border border-[#DBE5F5] rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-[#0B1F4B]"
              />
            </div>

            <div className="flex items-center justify-end gap-2 pt-2">
              <button
                onClick={() => setCancelModalTransfer(null)}
                className="px-4 py-2 text-xs font-semibold text-[#52627F] hover:bg-[#F8FAFC] rounded-xl cursor-pointer"
              >
                Keep Transfer
              </button>
              <button
                onClick={handleConfirmCancel}
                className="px-4 py-2 text-xs font-bold text-white bg-[#D9480F] hover:bg-orange-700 rounded-xl shadow-xs cursor-pointer"
              >
                Confirm Cancellation
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Batch Document Modal */}
      <SuratJalanModal
        transfer={selectedTransferForDoc}
        onClose={() => setSelectedTransferForDoc(null)}
      />
    </div>
  );
};
