import React, { useState } from 'react';
import { useApp } from '../../context/AppContext';
import { DrugMaster } from '../../types';
import {
  ShieldCheck,
  Building2,
  Sliders,
  Database,
  CheckCircle2,
  XCircle,
  Plus,
  RefreshCw,
  Snowflake,
} from 'lucide-react';

export const AdminView: React.FC = () => {
  const {
    facilities,
    drugs,
    scoreWeights,
    verifyFacility,
    updateScoreWeights,
    addDrug,
    resetDemoData,
  } = useApp();

  const [activeTab, setActiveTab] = useState<'verifications' | 'weights' | 'drugs'>('verifications');

  // New drug form state
  const [newDrugName, setNewDrugName] = useState('');
  const [newGenericName, setNewGenericName] = useState('');
  const [newForm, setNewForm] = useState<any>('Tablet');
  const [newStrength, setNewStrength] = useState('');
  const [newCategory, setNewCategory] = useState<any>('Antibiotics');
  const [newColdChain, setNewColdChain] = useState(false);
  const [newPrice, setNewPrice] = useState(35000);

  const pendingFacilities = facilities.filter((f) => f.verified_status === 'pending');

  const handleAddDrugSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newDrugName || !newGenericName) return;

    const drug: DrugMaster = {
      id: `drug_custom_${Date.now()}`,
      name: newDrugName,
      generic_name: newGenericName,
      form: newForm,
      strength: newStrength,
      category: newCategory,
      cold_chain: newColdChain,
      unit_price: Number(newPrice),
      min_stock_alert: 30,
    };

    addDrug(drug);
    setNewDrugName('');
    setNewGenericName('');
    setNewStrength('');
  };

  return (
    <div className="space-y-6 font-body text-slate-800">
      {/* Header */}
      <div className="bg-white p-6 rounded-2xl border border-[#DBE5F5] shadow-xs flex flex-wrap items-center justify-between gap-4">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF1FF] border border-[#DBE5F5] text-[#1D4ED8] text-xs font-bold mb-2">
            <ShieldCheck className="w-3.5 h-3.5 text-[#1D4ED8]" />
            <span>Platform Governance & System Administration</span>
          </div>
          <h2 className="text-xl font-bold text-[#0B1F4B]">
            System Administration & Algorithm Weights
          </h2>
          <p className="text-xs text-[#52627F] mt-1 max-w-2xl">
            Facility licensing validation, redistribution matching algorithm weight tuning, and master drug catalog maintenance.
          </p>
        </div>

        <button
          onClick={resetDemoData}
          className="px-4 py-2 text-xs font-bold bg-[#F8FAFC] hover:bg-[#EAF1FF] text-[#0B1F4B] border border-[#DBE5F5] rounded-xl flex items-center gap-2 transition-colors cursor-pointer shadow-xs"
          title="Reset dataset back to default demo data"
        >
          <RefreshCw className="w-3.5 h-3.5 text-[#1D4ED8]" />
          <span>Reset Demo Data</span>
        </button>
      </div>

      {/* Admin Tabs */}
      <div className="flex border-b border-[#DBE5F5] gap-4 text-xs font-bold">
        <button
          onClick={() => setActiveTab('verifications')}
          className={`pb-3 border-b-2 transition-colors cursor-pointer flex items-center gap-2 ${
            activeTab === 'verifications'
              ? 'border-[#1D4ED8] text-[#1D4ED8]'
              : 'border-transparent text-[#52627F] hover:text-[#0B1F4B]'
          }`}
        >
          <Building2 className="w-4 h-4" />
          <span>Facility Verifications ({pendingFacilities.length} Pending)</span>
        </button>
        <button
          onClick={() => setActiveTab('weights')}
          className={`pb-3 border-b-2 transition-colors cursor-pointer flex items-center gap-2 ${
            activeTab === 'weights'
              ? 'border-[#1D4ED8] text-[#1D4ED8]'
              : 'border-transparent text-[#52627F] hover:text-[#0B1F4B]'
          }`}
        >
          <Sliders className="w-4 h-4" />
          <span>Matching Algorithm Weights</span>
        </button>
        <button
          onClick={() => setActiveTab('drugs')}
          className={`pb-3 border-b-2 transition-colors cursor-pointer flex items-center gap-2 ${
            activeTab === 'drugs'
              ? 'border-[#1D4ED8] text-[#1D4ED8]'
              : 'border-transparent text-[#52627F] hover:text-[#0B1F4B]'
          }`}
        >
          <Database className="w-4 h-4" />
          <span>Master Drug Catalog ({drugs.length})</span>
        </button>
      </div>

      {/* Tab 1: Verifications (US-08) */}
      {activeTab === 'verifications' && (
        <div className="space-y-4">
          <div className="bg-white p-5 rounded-2xl border border-[#DBE5F5] shadow-xs space-y-4">
            <h3 className="text-sm font-bold text-[#0B1F4B]">
              Healthcare Facility Operational Licensing Verification Queue
            </h3>
            <p className="text-xs text-[#52627F]">
              In accordance with security and health authority rules, unverified facilities cannot request or receive medicine redistributions to safeguard official pharmacy supply chains.
            </p>

            <div className="divide-y divide-[#DBE5F5]">
              {facilities.map((fac) => {
                const isPending = fac.verified_status === 'pending';

                return (
                  <div
                    key={fac.id}
                    className="py-4 flex flex-wrap items-center justify-between gap-4"
                  >
                    <div>
                      <div className="flex items-center gap-2">
                        <h4 className="font-bold text-[#0B1F4B] text-sm">{fac.name}</h4>
                        <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-[#F8FAFC] text-[#52627F] border border-[#DBE5F5]">
                          {fac.type}
                        </span>
                        {fac.verified_status === 'verified' && (
                          <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] flex items-center gap-1">
                            <CheckCircle2 className="w-3 h-3 text-[#1D4ED8]" /> Verified
                          </span>
                        )}
                        {fac.verified_status === 'pending' && (
                          <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F8FAFC] text-[#B7791F] border border-[#B7791F]/30">
                            Pending Admin Verification
                          </span>
                        )}
                        {fac.verified_status === 'rejected' && (
                          <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F8FAFC] text-[#D9480F] border border-[#D9480F]/30">
                            Rejected
                          </span>
                        )}
                      </div>
                      <p className="text-xs text-[#52627F] mt-1">{fac.address}</p>
                      <p className="text-[11px] text-[#52627F] font-mono mt-0.5">
                        License No: {fac.license_number} • PIC: {fac.pic_name} ({fac.pic_phone})
                      </p>
                    </div>

                    <div className="flex items-center gap-2">
                      {isPending ? (
                        <>
                          <button
                            onClick={() => verifyFacility(fac.id, 'verified')}
                            className="px-3.5 py-1.5 bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold rounded-lg shadow-xs transition-colors cursor-pointer"
                          >
                            Approve License
                          </button>
                          <button
                            onClick={() => verifyFacility(fac.id, 'rejected')}
                            className="px-3.5 py-1.5 bg-[#F8FAFC] hover:bg-slate-100 text-[#D9480F] border border-[#D9480F]/30 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                          >
                            Reject
                          </button>
                        </>
                      ) : (
                        <button
                          onClick={() =>
                            verifyFacility(
                              fac.id,
                              fac.verified_status === 'verified' ? 'pending' : 'verified'
                            )
                          }
                          className="text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] cursor-pointer"
                        >
                          Change Status
                        </button>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      )}

      {/* Tab 2: Scoring Weights Adjustment */}
      {activeTab === 'weights' && (
        <div className="bg-white p-6 rounded-2xl border border-[#DBE5F5] shadow-xs space-y-6 max-w-2xl">
          <div>
            <h3 className="text-base font-bold text-[#0B1F4B]">
              Matching Algorithm Scoring Weight Configuration
            </h3>
            <p className="text-xs text-[#52627F] mt-1">
              Tune the relative weights of the recommendation engine (Deficit vs Distance vs Shelf Life).
              Combined target weight evaluates to 1.0 (100%).
            </p>
          </div>

          <div className="space-y-5 text-xs">
            {/* Shortage Weight */}
            <div className="space-y-1">
              <div className="flex justify-between font-bold text-[#0B1F4B]">
                <span>1. Stock Deficit Level Weight (Shortage Severity):</span>
                <span className="text-[#1D4ED8] font-mono">
                  {Math.round(scoreWeights.shortage_weight * 100)}%
                </span>
              </div>
              <input
                type="range"
                min="0.1"
                max="0.8"
                step="0.05"
                value={scoreWeights.shortage_weight}
                onChange={(e) =>
                  updateScoreWeights({ shortage_weight: parseFloat(e.target.value) })
                }
                className="w-full accent-[#1D4ED8] cursor-pointer"
              />
              <p className="text-[11px] text-[#52627F]">
                Prioritizes recipient facilities that are near stock-out exhaustion.
              </p>
            </div>

            {/* Distance Weight */}
            <div className="space-y-1">
              <div className="flex justify-between font-bold text-[#0B1F4B]">
                <span>2. Geographic Proximity Weight (Haversine Distance):</span>
                <span className="text-[#1D4ED8] font-mono">
                  {Math.round(scoreWeights.distance_weight * 100)}%
                </span>
              </div>
              <input
                type="range"
                min="0.1"
                max="0.8"
                step="0.05"
                value={scoreWeights.distance_weight}
                onChange={(e) =>
                  updateScoreWeights({ distance_weight: parseFloat(e.target.value) })
                }
                className="w-full accent-[#1D4ED8] cursor-pointer"
              />
              <p className="text-[11px] text-[#52627F]">
                Prioritizes facilities physically closer to minimize transit time and carbon footprint.
              </p>
            </div>

            {/* Shelf-Life Weight */}
            <div className="space-y-1">
              <div className="flex justify-between font-bold text-[#0B1F4B]">
                <span>3. Remaining Shelf-Life Feasibility Weight:</span>
                <span className="text-[#1D4ED8] font-mono">
                  {Math.round(scoreWeights.shelf_life_weight * 100)}%
                </span>
              </div>
              <input
                type="range"
                min="0.1"
                max="0.8"
                step="0.05"
                value={scoreWeights.shelf_life_weight}
                onChange={(e) =>
                  updateScoreWeights({ shelf_life_weight: parseFloat(e.target.value) })
                }
                className="w-full accent-[#1D4ED8] cursor-pointer"
              />
              <p className="text-[11px] text-[#52627F]">
                Ensures recipient facility has sufficient historical burn rate velocity to utilize the batch before expiration.
              </p>
            </div>

            {/* Forecast Window */}
            <div className="pt-3 border-t border-[#DBE5F5] space-y-1">
              <div className="flex justify-between font-bold text-[#0B1F4B]">
                <span>Burn Rate Moving Average Window:</span>
                <span className="text-[#1D4ED8] font-mono">{scoreWeights.forecast_window_days} Days</span>
              </div>
              <select
                value={scoreWeights.forecast_window_days}
                onChange={(e) =>
                  updateScoreWeights({ forecast_window_days: parseInt(e.target.value, 10) })
                }
                className="w-full p-2 bg-[#F8FAFC] border border-[#DBE5F5] rounded-lg outline-none font-semibold text-xs text-[#0B1F4B]"
              >
                <option value={30}>Last 30 Days (Responsive Default)</option>
                <option value={60}>Last 60 Days (Medium Window)</option>
                <option value={90}>Last 90 Days (Long-term Macro Trend)</option>
              </select>
            </div>
          </div>
        </div>
      )}

      {/* Tab 3: Drug Master Catalog */}
      {activeTab === 'drugs' && (
        <div className="space-y-6">
          <div className="bg-white p-6 rounded-2xl border border-[#DBE5F5] shadow-xs">
            <h3 className="text-sm font-bold text-[#0B1F4B] mb-3">Add New Medicine to National Master Catalog</h3>
            <form onSubmit={handleAddDrugSubmit} className="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
              <div>
                <label className="block font-semibold text-[#0B1F4B] mb-1">Brand Name & Strength:</label>
                <input
                  type="text"
                  required
                  placeholder="e.g. Azithromycin 500 mg"
                  value={newDrugName}
                  onChange={(e) => setNewDrugName(e.target.value)}
                  className="w-full p-2 border border-[#DBE5F5] rounded-lg outline-none text-[#0B1F4B]"
                />
              </div>
              <div>
                <label className="block font-semibold text-[#0B1F4B] mb-1">Generic Name:</label>
                <input
                  type="text"
                  required
                  placeholder="e.g. Azithromycin"
                  value={newGenericName}
                  onChange={(e) => setNewGenericName(e.target.value)}
                  className="w-full p-2 border border-[#DBE5F5] rounded-lg outline-none text-[#0B1F4B]"
                />
              </div>
              <div>
                <label className="block font-semibold text-[#0B1F4B] mb-1">Dosage Form:</label>
                <select
                  value={newForm}
                  onChange={(e) => setNewForm(e.target.value as any)}
                  className="w-full p-2 border border-[#DBE5F5] rounded-lg outline-none bg-white text-[#0B1F4B]"
                >
                  <option value="Tablet">Tablet</option>
                  <option value="Capsule">Capsule</option>
                  <option value="Syrup">Syrup</option>
                  <option value="Injection">Injection</option>
                  <option value="Ointment">Ointment</option>
                </select>
              </div>

              <div>
                <label className="block font-semibold text-[#0B1F4B] mb-1">Therapeutic Category:</label>
                <select
                  value={newCategory}
                  onChange={(e) => setNewCategory(e.target.value as any)}
                  className="w-full p-2 border border-[#DBE5F5] rounded-lg outline-none bg-white text-[#0B1F4B]"
                >
                  <option value="Antibiotics">Antibiotics</option>
                  <option value="Analgesics">Analgesics</option>
                  <option value="Cardiovascular">Cardiovascular</option>
                  <option value="Antidiabetic">Antidiabetic</option>
                  <option value="Fluids & Electrolytes">Fluids & Electrolytes</option>
                  <option value="Cold Chain (Vaccine/Insulin)">Cold Chain (Vaccine/Insulin)</option>
                </select>
              </div>
              <div>
                <label className="block font-semibold text-[#0B1F4B] mb-1">Unit Price Estimate (IDR):</label>
                <input
                  type="number"
                  value={newPrice}
                  onChange={(e) => setNewPrice(parseInt(e.target.value, 10) || 0)}
                  className="w-full p-2 border border-[#DBE5F5] rounded-lg outline-none text-[#0B1F4B]"
                />
              </div>
              <div className="flex items-end gap-3">
                <label className="flex items-center gap-2 font-semibold text-[#0B1F4B] cursor-pointer h-10">
                  <input
                    type="checkbox"
                    checked={newColdChain}
                    onChange={(e) => setNewColdChain(e.target.checked)}
                    className="rounded text-[#1D4ED8] accent-[#1D4ED8]"
                  />
                  <span>Cold Chain Required ❄️</span>
                </label>
                <button
                  type="submit"
                  className="px-4 py-2 bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold rounded-lg shadow-xs cursor-pointer ml-auto transition-colors"
                >
                  Save Medicine
                </button>
              </div>
            </form>
          </div>

          {/* List of existing master drugs */}
          <div className="bg-white rounded-2xl border border-[#DBE5F5] overflow-hidden shadow-xs">
            <div className="p-4 bg-[#F8FAFC] border-b border-[#DBE5F5] font-bold text-xs text-[#0B1F4B]">
              Registered National Medicine Catalog ({drugs.length})
            </div>
            <div className="divide-y divide-[#DBE5F5] max-h-96 overflow-y-auto">
              {drugs.map((d) => (
                <div key={d.id} className="p-3.5 flex items-center justify-between text-xs hover:bg-[#F8FAFC]">
                  <div className="flex items-center gap-2.5">
                    <div>
                      <p className="font-bold text-[#0B1F4B]">{d.name}</p>
                      <p className="text-[11px] text-[#52627F] font-mono">
                        {d.generic_name} • {d.strength} • {d.form}
                      </p>
                    </div>
                    {d.cold_chain && (
                      <span className="px-2 py-0.5 rounded bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] text-[10px] font-bold">
                        ❄️ Cold Chain
                      </span>
                    )}
                  </div>
                  <div className="text-right">
                    <span className="font-bold text-[#0B1F4B]">
                      Rp {d.unit_price.toLocaleString('en-US')}
                    </span>
                    <span className="text-[11px] text-[#52627F] block">{d.category}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
