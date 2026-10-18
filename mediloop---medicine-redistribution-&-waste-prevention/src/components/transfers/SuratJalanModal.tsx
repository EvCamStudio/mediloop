import React from 'react';
import { useApp } from '../../context/AppContext';
import { TransferRequest } from '../../types';
import { Printer, X, ShieldCheck, FileText, AlertCircle } from 'lucide-react';

interface SuratJalanModalProps {
  transfer: TransferRequest | null;
  onClose: () => void;
}

export const SuratJalanModal: React.FC<SuratJalanModalProps> = ({ transfer, onClose }) => {
  const { facilities, drugs, batches } = useApp();

  if (!transfer) return null;

  const sender = facilities.find((f) => f.id === transfer.from_facility_id);
  const receiver = facilities.find((f) => f.id === transfer.to_facility_id);
  const drug = drugs.find((d) => d.id === transfer.drug_id);
  const batch = batches.find((b) => b.id === transfer.batch_id);

  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 font-body">
      <div className="bg-white rounded-2xl max-w-3xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-[#DBE5F5] overflow-hidden">
        {/* Action Header */}
        <div className="px-6 py-3.5 bg-[#F8FAFC] border-b border-[#DBE5F5] flex items-center justify-between no-print">
          <div className="flex items-center gap-2">
            <FileText className="w-5 h-5 text-[#1D4ED8]" />
            <h3 className="text-sm font-bold text-[#0B1F4B]">
              Batch Document
            </h3>
          </div>
          <div className="flex items-center gap-2">
            <button
              onClick={handlePrint}
              className="px-4 py-1.5 text-xs font-bold bg-[#1D4ED8] text-white rounded-xl hover:bg-blue-800 flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer"
            >
              <Printer className="w-3.5 h-3.5" />
              <span>Print / Save PDF</span>
            </button>
            <button
              onClick={onClose}
              className="p-1.5 rounded-lg text-[#52627F] hover:text-[#0B1F4B] hover:bg-slate-200/60 transition-colors cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Printable Document Body */}
        <div className="p-8 overflow-y-auto flex-1 text-[#0B1F4B]" id="printable-document">
          {/* Header Kop */}
          <div className="border-b-2 border-[#0B1F4B] pb-4 text-center relative">
            <div className="flex items-center justify-center gap-3">
              <div className="w-12 h-12 rounded-xl bg-[#1D4ED8] text-white flex items-center justify-center font-black text-xl">
                ML
              </div>
              <div>
                <h2 className="text-base font-extrabold tracking-wider uppercase text-[#0B1F4B]">
                  DISTRICT HEALTH PHARMACEUTICAL NETWORK
                </h2>
                <h3 className="text-sm font-bold text-[#1D4ED8]">
                  BATCH DOCUMENT & REDISTRIBUTION PROTOCOL
                </h3>
                <p className="text-xs text-[#52627F] mt-1 font-semibold">
                  Recommendation document, not a regulatory authorization.
                </p>
              </div>
            </div>

            {/* Document metadata badges */}
            <div className="mt-4 pt-2 border-t border-[#DBE5F5] flex flex-wrap justify-between items-center text-xs text-[#52627F]">
              <div>
                <span className="font-semibold text-[#0B1F4B]">Transfer Number:</span>{' '}
                <span className="font-mono font-bold text-[#1D4ED8]">{transfer.transfer_code}</span>
              </div>
              <div>
                <span className="font-semibold text-[#0B1F4B]">Document Ref:</span>{' '}
                <span className="font-mono font-bold text-[#0B1F4B]">
                  {transfer.handover_document_number || 'BAST/MDN/2026/041'}
                </span>
              </div>
              <div>
                <span className="font-semibold text-[#0B1F4B]">Issue Date:</span>{' '}
                <span className="font-medium">
                  {new Date(transfer.created_at).toLocaleDateString('en-US', { dateStyle: 'long' })}
                </span>
              </div>
            </div>
          </div>

          {/* Parties Involved: Origin & Destination */}
          <div className="grid grid-cols-2 gap-6 my-6 text-xs">
            {/* Origin */}
            <div className="p-4 bg-[#F8FAFC] rounded-xl border border-[#DBE5F5]">
              <span className="text-[10px] font-bold uppercase tracking-wider text-[#1D4ED8] bg-[#EAF1FF] px-2 py-0.5 rounded border border-[#DBE5F5]">
                ORIGIN (SENDER)
              </span>
              <h4 className="font-bold text-sm text-[#0B1F4B] mt-2">{sender?.name}</h4>
              <p className="text-[#52627F] mt-0.5">{sender?.address}</p>
              <div className="mt-2 pt-2 border-t border-[#DBE5F5] space-y-0.5 text-[#52627F]">
                <p>
                  <span className="font-semibold text-[#0B1F4B]">Responsible Pharmacist:</span> {sender?.pic_name}
                </p>
                <p>
                  <span className="font-semibold text-[#0B1F4B]">License / SIPA:</span> {sender?.license_number}
                </p>
                <p>
                  <span className="font-semibold text-[#0B1F4B]">Contact:</span> {sender?.pic_phone}
                </p>
              </div>
            </div>

            {/* Destination */}
            <div className="p-4 bg-[#F8FAFC] rounded-xl border border-[#DBE5F5]">
              <span className="text-[10px] font-bold uppercase tracking-wider text-[#1D4ED8] bg-[#EAF1FF] px-2 py-0.5 rounded border border-[#DBE5F5]">
                DESTINATION (RECEIVER)
              </span>
              <h4 className="font-bold text-sm text-[#0B1F4B] mt-2">{receiver?.name}</h4>
              <p className="text-[#52627F] mt-0.5">{receiver?.address}</p>
              <div className="mt-2 pt-2 border-t border-[#DBE5F5] space-y-0.5 text-[#52627F]">
                <p>
                  <span className="font-semibold text-[#0B1F4B]">Responsible Pharmacist:</span> {receiver?.pic_name}
                </p>
                <p>
                  <span className="font-semibold text-[#0B1F4B]">License / SIPA:</span> {receiver?.license_number}
                </p>
                <p>
                  <span className="font-semibold text-[#0B1F4B]">Contact:</span> {receiver?.pic_phone}
                </p>
              </div>
            </div>
          </div>

          {/* Table of Medicines */}
          <div className="my-6">
            <h4 className="text-xs font-bold text-[#0B1F4B] uppercase tracking-wider mb-2">
              Batch & Quantity Specification
            </h4>
            <div className="border border-[#DBE5F5] rounded-xl overflow-hidden">
              <table className="w-full text-left text-xs">
                <thead className="bg-[#F8FAFC] border-b border-[#DBE5F5] text-[#52627F] font-bold">
                  <tr>
                    <th className="py-2.5 px-3">No</th>
                    <th className="py-2.5 px-3">Medicine Name</th>
                    <th className="py-2.5 px-3">Batch Number</th>
                    <th className="py-2.5 px-3">Expiry Date</th>
                    <th className="py-2.5 px-3 text-right">Quantity</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#DBE5F5]">
                  <tr>
                    <td className="py-3 px-3">1</td>
                    <td className="py-3 px-3">
                      <p className="font-bold text-[#0B1F4B]">{drug?.name}</p>
                      <p className="text-[11px] text-[#52627F] font-mono">{drug?.generic_name}</p>
                    </td>
                    <td className="py-3 px-3 font-mono font-bold text-[#0B1F4B]">
                      #{batch?.batch_no || 'AMX-2025-09A'}
                    </td>
                    <td className="py-3 px-3 font-bold text-[#D9480F]">
                      {batch?.expiry_date || '2026-12-02'}
                    </td>
                    <td className="py-3 px-3 text-right font-extrabold text-[#0B1F4B] text-sm">
                      {transfer.qty} boxes
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          {/* Logistics Courier info */}
          <div className="p-3.5 bg-[#F8FAFC] border border-[#DBE5F5] rounded-xl text-xs flex items-center justify-between">
            <div>
              <p className="font-semibold text-[#0B1F4B]">
                Logistics Courier:{' '}
                <span className="font-normal text-[#52627F]">
                  {transfer.courier_name || 'District Health Logistics (Rizal)'}
                </span>
              </p>
              <p className="text-[#52627F] text-[11px] mt-0.5">
                Vehicle Plate Number:{' '}
                <span className="font-mono font-bold text-[#0B1F4B]">
                  {transfer.courier_vehicle_plate || 'BK 4912 KD'}
                </span>
              </p>
            </div>
            {drug?.cold_chain && (
              <div className="px-3 py-1 bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] rounded-lg font-bold text-xs flex items-center gap-1.5">
                <span>Cold Chain Mandatory (2°C – 8°C)</span>
              </div>
            )}
          </div>

          {/* Mandatory Explicit Disclaimer */}
          <div className="mt-4 p-3.5 bg-[#EAF1FF] border border-[#DBE5F5] rounded-xl text-xs text-[#1D4ED8] flex items-start gap-2.5">
            <AlertCircle className="w-4 h-4 text-[#1D4ED8] shrink-0 mt-0.5" />
            <div className="space-y-0.5">
              <p className="font-bold text-[#0B1F4B]">
                Recommendation document, not a regulatory authorization.
              </p>
              <p className="text-xs text-[#52627F]">
                This transfer recommendation must be officially approved through district health channels. Stock balances change only after physical receipt verification by the receiving facility.
              </p>
            </div>
          </div>

          {/* Verification Signatures */}
          <div className="grid grid-cols-3 gap-4 mt-8 pt-4 border-t border-[#DBE5F5] text-center text-xs">
            <div>
              <p className="text-[#52627F]">Origin Sender,</p>
              <div className="h-16 flex items-center justify-center">
                <span className="text-[#52627F] italic font-mono text-[11px]">[Digital Verification Verified]</span>
              </div>
              <p className="font-bold underline text-[#0B1F4B]">{sender?.pic_name}</p>
              <p className="text-[10px] text-[#52627F]">Lead Pharmacist</p>
            </div>

            <div>
              <p className="text-[#52627F]">District Pharmacy Unit,</p>
              <div className="h-16 flex items-center justify-center">
                <div className="border border-[#DBE5F5] bg-[#EAF1FF] px-2.5 py-1 rounded text-[10px] text-[#1D4ED8] font-bold">
                  DISTRICT APPROVED
                </div>
              </div>
              <p className="font-bold underline text-[#0B1F4B]">Dra. Hj. Nurhaliza, Apt</p>
              <p className="text-[10px] text-[#52627F]">District Pharmacy Logistics Unit</p>
            </div>

            <div>
              <p className="text-[#52627F]">Destination Receiver,</p>
              <div className="h-16 flex items-center justify-center">
                <span className="text-[#52627F] italic font-mono text-[11px]">
                  {transfer.status === 'Completed' ? '[Receipt Verified]' : '[Pending Receipt]'}
                </span>
              </div>
              <p className="font-bold underline text-[#0B1F4B]">{receiver?.pic_name}</p>
              <p className="text-[10px] text-[#52627F]">Receiving Pharmacist</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
