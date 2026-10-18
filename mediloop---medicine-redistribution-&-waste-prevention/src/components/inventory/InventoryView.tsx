import React, { useState } from 'react';
import { useApp } from '../../context/AppContext';
import { DrugBatch } from '../../types';
import { CsvImportModal } from './CsvImportModal';
import { Plus, UploadCloud, Search, Filter, AlertTriangle, Snowflake, Edit3, Check } from 'lucide-react';

interface InventoryViewProps {
  onFindMatch?: (batchId: string) => void;
}

export const InventoryView: React.FC<InventoryViewProps> = ({ onFindMatch }) => {
  const { batches, drugs, currentFacility, addBatch, updateBatch } = useApp();

  const [isCsvModalOpen, setIsCsvModalOpen] = useState<boolean>(false);
  const [isAddModalOpen, setIsAddModalOpen] = useState<boolean>(false);
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [filterCategory, setFilterCategory] = useState<string>('all');

  // Form state for adding batch
  const [newDrugId, setNewDrugId] = useState<string>(drugs[0]?.id || '');
  const [newBatchNo, setNewBatchNo] = useState<string>('');
  const [newExpiryDate, setNewExpiryDate] = useState<string>('');
  const [newStock, setNewStock] = useState<number>(100);
  const [newSupplier, setNewSupplier] = useState<string>('PT Kimia Farma Trading');

  // Facility batches
  const facilityBatches = batches.filter((b) => b.facility_id === currentFacility.id);

  const filteredBatches = facilityBatches.filter((b) => {
    const drug = drugs.find((d) => d.id === b.drug_id);
    const matchesSearch =
      drug?.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      b.batch_no.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCat = filterCategory === 'all' || drug?.category === filterCategory;
    return matchesSearch && matchesCat;
  });

  const handleAddBatchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newBatchNo || !newExpiryDate) return;

    addBatch({
      facility_id: currentFacility.id,
      drug_id: newDrugId,
      batch_no: newBatchNo,
      expiry_date: newExpiryDate,
      stock: Number(newStock),
      source_supplier: newSupplier,
    });

    setIsAddModalOpen(false);
    setNewBatchNo('');
    setNewExpiryDate('');
  };

  return (
    <div className="space-y-6 font-body text-slate-800">
      {/* Top Header */}
      <div className="bg-white p-6 rounded-2xl border border-[#DBE5F5] shadow-xs flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 className="text-xl font-bold text-[#0B1F4B]">
            Medicine Inventory & Batch Stock
          </h2>
          <p className="text-xs text-[#52627F] mt-1">
            Active Facility: <span className="font-semibold text-[#1D4ED8]">{currentFacility.name}</span> •{' '}
            {facilityBatches.length} batches recorded in system
          </p>
        </div>

        <div className="flex items-center gap-2.5">
          <button
            onClick={() => setIsCsvModalOpen(true)}
            className="px-4 py-2.5 text-xs font-bold text-[#0B1F4B] bg-white border border-[#DBE5F5] hover:bg-[#F8FAFC] rounded-xl flex items-center gap-2 shadow-xs transition-colors cursor-pointer"
          >
            <UploadCloud className="w-4 h-4 text-[#1D4ED8]" />
            <span>Import CSV</span>
          </button>
          <button
            onClick={() => setIsAddModalOpen(true)}
            className="px-4 py-2.5 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-xl flex items-center gap-2 shadow-xs transition-all hover:scale-[1.02] cursor-pointer"
          >
            <Plus className="w-4 h-4" />
            <span>Add New Batch</span>
          </button>
        </div>
      </div>

      {/* Filter & Search Bar */}
      <div className="bg-white p-4 rounded-2xl border border-[#DBE5F5] shadow-xs flex flex-wrap items-center justify-between gap-3 text-sm">
        <div className="flex items-center gap-2 bg-[#F8FAFC] border border-[#DBE5F5] rounded-xl px-3 py-2 w-full sm:w-72">
          <Search className="w-4 h-4 text-[#52627F]" />
          <input
            type="text"
            placeholder="Search medicine name or batch #..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="bg-transparent text-xs text-[#0B1F4B] placeholder-[#52627F] outline-none w-full"
          />
        </div>

        <div className="flex items-center gap-2">
          <span className="text-xs font-semibold text-[#52627F]">Medicine Category:</span>
          <select
            value={filterCategory}
            onChange={(e) => setFilterCategory(e.target.value)}
            className="text-xs bg-[#F8FAFC] border border-[#DBE5F5] rounded-xl px-3 py-2 text-[#0B1F4B] font-medium outline-none"
          >
            <option value="all">All Categories</option>
            <option value="Antibiotics">Antibiotics</option>
            <option value="Analgesics">Analgesics</option>
            <option value="Cardiovascular">Cardiovascular</option>
            <option value="Antidiabetic">Antidiabetic</option>
            <option value="Cold Chain (Vaccine/Insulin)">Cold Chain</option>
            <option value="Fluids & Electrolytes">Fluids & Electrolytes</option>
          </select>
        </div>
      </div>

      {/* Batches Table */}
      <div className="bg-white rounded-2xl border border-[#DBE5F5] overflow-hidden shadow-xs">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-[#F8FAFC] text-[#52627F] font-bold border-b border-[#DBE5F5]">
              <tr>
                <th className="py-3 px-4">Medicine Name</th>
                <th className="py-3 px-4">Category & Form</th>
                <th className="py-3 px-4">Batch #</th>
                <th className="py-3 px-4">Expiry Date</th>
                <th className="py-3 px-4">Stock on Hand</th>
                <th className="py-3 px-4">Distributor</th>
                <th className="py-3 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#DBE5F5]">
              {filteredBatches.map((b) => {
                const drug = drugs.find((d) => d.id === b.drug_id);
                const isExpiringSoon =
                  (new Date(b.expiry_date).getTime() - new Date().getTime()) /
                    (1000 * 60 * 60 * 24) <
                  60;

                return (
                  <tr key={b.id} className="hover:bg-[#F8FAFC] transition-colors">
                    <td className="py-3 px-4">
                      <div className="flex items-center gap-2">
                        <div>
                          <p className="font-bold text-[#0B1F4B]">{drug?.name}</p>
                          <p className="text-[11px] text-[#52627F] font-mono">{drug?.generic_name}</p>
                        </div>
                        {drug?.cold_chain && (
                          <span
                            className="p-1 rounded bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]"
                            title="Cold Chain 2-8°C Required"
                          >
                            <Snowflake className="w-3.5 h-3.5" />
                          </span>
                        )}
                      </div>
                    </td>
                    <td className="py-3 px-4">
                      <span className="px-2 py-0.5 rounded-md bg-[#F1F5F9] text-[#0B1F4B] font-medium text-[11px]">
                        {drug?.category} • {drug?.form}
                      </span>
                    </td>
                    <td className="py-3 px-4 font-mono font-bold text-[#0B1F4B]">#{b.batch_no}</td>
                    <td className="py-3 px-4">
                      <span
                        className={`font-semibold ${
                          isExpiringSoon ? 'text-[#D9480F] font-bold' : 'text-[#0B1F4B]'
                        }`}
                      >
                        {b.expiry_date}
                      </span>
                      {isExpiringSoon && (
                        <span className="ml-1.5 text-[10px] text-[#D9480F] bg-[#F8FAFC] border border-[#D9480F]/30 px-1.5 py-0.5 rounded font-bold">
                          Near Expiry
                        </span>
                      )}
                    </td>
                    <td className="py-3 px-4">
                      <span className="font-extrabold text-[#0B1F4B] text-sm">{b.stock}</span>{' '}
                      <span className="text-[#52627F]">boxes</span>
                    </td>
                    <td className="py-3 px-4 text-[#52627F] text-[11px]">{b.source_supplier || '-'}</td>
                    <td className="py-3 px-4 text-right">
                      {onFindMatch && (
                        <button
                          onClick={() => onFindMatch(b.id)}
                          className="px-3 py-1.5 bg-[#EAF1FF] text-[#1D4ED8] hover:bg-[#1D4ED8] hover:text-white border border-[#DBE5F5] rounded-lg text-xs font-semibold transition-all cursor-pointer"
                        >
                          Find Recipient
                        </button>
                      )}
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>

      {/* CSV Import Modal */}
      <CsvImportModal isOpen={isCsvModalOpen} onClose={() => setIsCsvModalOpen(false)} />

      {/* Add Batch Modal */}
      {isAddModalOpen && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#DBE5F5]">
            <h3 className="text-base font-bold text-[#0B1F4B] mb-4">
              Add New Medicine Batch Manually
            </h3>
            <form onSubmit={handleAddBatchSubmit} className="space-y-4 text-xs">
              <div>
                <label className="block font-bold text-[#0B1F4B] mb-1">Select Medicine Master:</label>
                <select
                  value={newDrugId}
                  onChange={(e) => setNewDrugId(e.target.value)}
                  className="w-full p-2.5 bg-[#F8FAFC] border border-[#DBE5F5] rounded-xl font-medium outline-none text-[#0B1F4B]"
                >
                  {drugs.map((d) => (
                    <option key={d.id} value={d.id}>
                      {d.name} ({d.form})
                    </option>
                  ))}
                </select>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold text-[#0B1F4B] mb-1">Batch Number:</label>
                  <input
                    type="text"
                    required
                    placeholder="e.g. AMX-2026-09"
                    value={newBatchNo}
                    onChange={(e) => setNewBatchNo(e.target.value)}
                    className="w-full p-2.5 border border-[#DBE5F5] rounded-xl outline-none font-mono text-[#0B1F4B]"
                  />
                </div>
                <div>
                  <label className="block font-bold text-[#0B1F4B] mb-1">Expiry Date:</label>
                  <input
                    type="date"
                    required
                    value={newExpiryDate}
                    onChange={(e) => setNewExpiryDate(e.target.value)}
                    className="w-full p-2.5 border border-[#DBE5F5] rounded-xl outline-none text-[#0B1F4B]"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold text-[#0B1F4B] mb-1">Stock Quantity (Boxes):</label>
                  <input
                    type="number"
                    min="1"
                    required
                    value={newStock}
                    onChange={(e) => setNewStock(parseInt(e.target.value, 10) || 0)}
                    className="w-full p-2.5 border border-[#DBE5F5] rounded-xl outline-none font-bold text-[#0B1F4B]"
                  />
                </div>
                <div>
                  <label className="block font-bold text-[#0B1F4B] mb-1">Distributor / Supplier:</label>
                  <input
                    type="text"
                    value={newSupplier}
                    onChange={(e) => setNewSupplier(e.target.value)}
                    className="w-full p-2.5 border border-[#DBE5F5] rounded-xl outline-none text-[#0B1F4B]"
                  />
                </div>
              </div>

              <div className="flex items-center justify-end gap-2 pt-4 border-t border-[#DBE5F5]">
                <button
                  type="button"
                  onClick={() => setIsAddModalOpen(false)}
                  className="px-4 py-2 text-[#52627F] font-semibold hover:bg-[#F8FAFC] rounded-xl cursor-pointer"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-xl shadow-xs cursor-pointer"
                >
                  Save Batch
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
