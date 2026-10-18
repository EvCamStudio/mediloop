import React, { createContext, useContext, useEffect, useMemo, useState } from 'react';
import {
  DrugBatch,
  DrugMaster,
  ExpiryForecast,
  HealthFacility,
  ImpactMetrics,
  ScoreWeights,
  TransferRequest,
  TransferStatus,
  UserRole,
} from '../types';
import {
  DEFAULT_SCORE_WEIGHTS,
  INITIAL_BATCHES,
  INITIAL_BURN_RATES,
  INITIAL_DRUGS,
  INITIAL_FACILITIES,
  INITIAL_TRANSFERS,
} from '../data/seedData';
import { computeBatchForecast } from '../utils/calculations';

interface AppContextType {
  facilities: HealthFacility[];
  drugs: DrugMaster[];
  batches: DrugBatch[];
  burnRates: Record<string, number>;
  transfers: TransferRequest[];
  scoreWeights: ScoreWeights;
  currentRole: UserRole;
  currentFacilityId: string;
  currentFacility: HealthFacility;
  forecasts: ExpiryForecast[];
  impactMetrics: ImpactMetrics;
  switchRole: (role: UserRole, facilityId?: string) => void;
  addBatch: (batch: Omit<DrugBatch, 'id' | 'updated_at'>) => void;
  updateBatch: (id: string, updates: Partial<DrugBatch>) => void;
  importBatches: (newBatches: DrugBatch[]) => void;
  createTransfer: (batchId: string, toFacilityId: string, qty: number, notes?: string) => TransferRequest;
  updateTransferStatus: (
    transferId: string,
    newStatus: TransferStatus,
    note?: string,
    rejectionReason?: string,
    courierDetails?: { name: string; plate: string }
  ) => void;
  updateScoreWeights: (weights: Partial<ScoreWeights>) => void;
  verifyFacility: (facilityId: string, status: 'verified' | 'rejected' | 'pending') => void;
  addDrug: (drug: DrugMaster) => void;
  resetDemoData: () => void;
}

const AppContext = createContext<AppContextType | undefined>(undefined);

const STORAGE_KEY_PREFIX = 'mediloop_v3_';

export const AppProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [facilities, setFacilities] = useState<HealthFacility[]>(() => {
    const saved = localStorage.getItem(`${STORAGE_KEY_PREFIX}facilities`);
    return saved ? JSON.parse(saved) : INITIAL_FACILITIES;
  });

  const [drugs, setDrugs] = useState<DrugMaster[]>(() => {
    const saved = localStorage.getItem(`${STORAGE_KEY_PREFIX}drugs`);
    return saved ? JSON.parse(saved) : INITIAL_DRUGS;
  });

  const [batches, setBatches] = useState<DrugBatch[]>(() => {
    const saved = localStorage.getItem(`${STORAGE_KEY_PREFIX}batches`);
    return saved ? JSON.parse(saved) : INITIAL_BATCHES;
  });

  const [burnRates, setBurnRates] = useState<Record<string, number>>(() => {
    const saved = localStorage.getItem(`${STORAGE_KEY_PREFIX}burnRates`);
    return saved ? JSON.parse(saved) : INITIAL_BURN_RATES;
  });

  const [transfers, setTransfers] = useState<TransferRequest[]>(() => {
    const saved = localStorage.getItem(`${STORAGE_KEY_PREFIX}transfers`);
    return saved ? JSON.parse(saved) : INITIAL_TRANSFERS;
  });

  const [scoreWeights, setScoreWeights] = useState<ScoreWeights>(() => {
    const saved = localStorage.getItem(`${STORAGE_KEY_PREFIX}weights`);
    return saved ? JSON.parse(saved) : DEFAULT_SCORE_WEIGHTS;
  });

  const [currentRole, setCurrentRole] = useState<UserRole>('pharmacist');
  const [currentFacilityId, setCurrentFacilityId] = useState<string>('fac_simpang');

  // Save changes to localStorage
  useEffect(() => {
    localStorage.setItem(`${STORAGE_KEY_PREFIX}facilities`, JSON.stringify(facilities));
  }, [facilities]);

  useEffect(() => {
    localStorage.setItem(`${STORAGE_KEY_PREFIX}drugs`, JSON.stringify(drugs));
  }, [drugs]);

  useEffect(() => {
    localStorage.setItem(`${STORAGE_KEY_PREFIX}batches`, JSON.stringify(batches));
  }, [batches]);

  useEffect(() => {
    localStorage.setItem(`${STORAGE_KEY_PREFIX}burnRates`, JSON.stringify(burnRates));
  }, [burnRates]);

  useEffect(() => {
    localStorage.setItem(`${STORAGE_KEY_PREFIX}transfers`, JSON.stringify(transfers));
  }, [transfers]);

  useEffect(() => {
    localStorage.setItem(`${STORAGE_KEY_PREFIX}weights`, JSON.stringify(scoreWeights));
  }, [scoreWeights]);

  // Current active facility
  const currentFacility = useMemo(() => {
    return facilities.find((f) => f.id === currentFacilityId) || facilities[0];
  }, [facilities, currentFacilityId]);

  // Automatically compute forecasts for all batches
  const forecasts = useMemo(() => {
    return batches.map((batch) => {
      const burnRateKey = `${batch.facility_id}_${batch.drug_id}`;
      const burnRate = burnRates[burnRateKey] || 2.0;
      return computeBatchForecast(batch, burnRate, 30);
    });
  }, [batches, burnRates]);

  // Automatically calculate cumulative impact metrics from Completed transfers
  const impactMetrics = useMemo<ImpactMetrics>(() => {
    const completedTransfers = transfers.filter((t) => t.status === 'Completed');
    let totalUnits = 0;
    let totalValue = 0;
    let totalStockOutDays = 0;
    const assistedFacilityIds = new Set<string>();

    for (const t of completedTransfers) {
      totalUnits += t.qty;
      const drug = drugs.find((d) => d.id === t.drug_id);
      if (drug) {
        totalValue += t.qty * drug.unit_price;
      }
      assistedFacilityIds.add(t.to_facility_id);

      const targetBurnRateKey = `${t.to_facility_id}_${t.drug_id}`;
      const targetBurnRate = burnRates[targetBurnRateKey] || 3.5;
      totalStockOutDays += Math.round(t.qty / targetBurnRate);
    }

    // CO2 and medical waste prevention: avg 0.052 kg CO2 eq / medical incinerator waste avoided per box
    const co2Avoided = Math.round(totalUnits * 0.052 * 10) / 10;

    return {
      total_units_saved: totalUnits,
      total_value_saved_idr: totalValue,
      co2_waste_prevented_kg: co2Avoided,
      stock_out_days_prevented: totalStockOutDays,
      facilities_assisted: assistedFacilityIds.size,
      transfers_completed: completedTransfers.length,
    };
  }, [transfers, drugs, burnRates]);

  const switchRole = (role: UserRole, facilityId?: string) => {
    setCurrentRole(role);
    if (facilityId) {
      setCurrentFacilityId(facilityId);
    } else {
      if (role === 'district_unit') {
        setCurrentFacilityId('fac_dinkes');
      } else if (role === 'receiver') {
        setCurrentFacilityId('fac_sentosa');
      } else if (role === 'pharmacist') {
        setCurrentFacilityId('fac_simpang');
      }
    }
  };

  const addBatch = (batchData: Omit<DrugBatch, 'id' | 'updated_at'>) => {
    const newBatch: DrugBatch = {
      ...batchData,
      id: `batch_${Date.now()}_${Math.random().toString(36).substring(2, 6)}`,
      updated_at: new Date().toISOString(),
    };
    setBatches((prev) => [newBatch, ...prev]);
  };

  const updateBatch = (id: string, updates: Partial<DrugBatch>) => {
    setBatches((prev) =>
      prev.map((b) => (b.id === id ? { ...b, ...updates, updated_at: new Date().toISOString() } : b))
    );
  };

  const importBatches = (newBatches: DrugBatch[]) => {
    setBatches((prev) => {
      // Merge: replace existing if matching batch_no & facility_id, otherwise append
      const updated = [...prev];
      for (const nb of newBatches) {
        const idx = updated.findIndex(
          (b) => b.batch_no === nb.batch_no && b.facility_id === nb.facility_id
        );
        if (idx >= 0) {
          updated[idx] = nb;
        } else {
          updated.push(nb);
        }
      }
      return updated;
    });
  };

  const createTransfer = (
    batchId: string,
    toFacilityId: string,
    qty: number,
    notes?: string
  ): TransferRequest => {
    const batch = batches.find((b) => b.id === batchId);
    if (!batch) throw new Error('Batch not found');

    const transferCode = `TRF/${new Date().getFullYear()}/${String(new Date().getMonth() + 1).padStart(
      2,
      '0'
    )}/${String(transfers.length + 1).padStart(3, '0')}`;

    const newTransfer: TransferRequest = {
      id: `trf-${Date.now()}`,
      transfer_code: transferCode,
      batch_id: batchId,
      drug_id: batch.drug_id,
      from_facility_id: batch.facility_id,
      to_facility_id: toFacilityId,
      qty,
      status: 'Requested',
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      notes: notes || 'Surplus batch redistribution request',
      handover_document_number: `BAST-OBAT/MDN/2026/${String(transfers.length + 42).padStart(3, '0')}`,
      history: [
        {
          id: `ev-${Date.now()}`,
          transfer_id: `trf-${Date.now()}`,
          actor_name: currentFacility.pic_name,
          actor_role: currentRole === 'pharmacist' ? 'Facility Pharmacist' : 'Facility Head',
          action: `Redistribution request created (${qty} boxes)`,
          timestamp: new Date().toISOString(),
        },
      ],
    };

    setTransfers((prev) => [newTransfer, ...prev]);
    return newTransfer;
  };

  const updateTransferStatus = (
    transferId: string,
    newStatus: TransferStatus,
    note?: string,
    rejectionReason?: string,
    courierDetails?: { name: string; plate: string }
  ) => {
    setTransfers((prev) =>
      prev.map((t) => {
        if (t.id !== transferId) return t;

        const newEvent = {
          id: `ev-${Date.now()}-${Math.random().toString(36).substring(2, 5)}`,
          transfer_id: t.id,
          actor_name:
            currentRole === 'district_unit'
              ? 'Dra. Hj. Nurhaliza, Apt'
              : currentFacility.pic_name,
          actor_role:
            currentRole === 'district_unit'
              ? 'District Health Pharmacy Unit'
              : currentRole === 'pharmacist'
              ? 'Facility Pharmacist'
              : 'Admin',
          action: `Status updated to: ${newStatus}${note ? ` (${note})` : ''}`,
          note: note || rejectionReason,
          timestamp: new Date().toISOString(),
        };

        const updated: TransferRequest = {
          ...t,
          status: newStatus,
          updated_at: new Date().toISOString(),
          rejection_reason: rejectionReason || t.rejection_reason,
          history: [...t.history, newEvent],
        };

        if (courierDetails) {
          updated.courier_name = courierDetails.name;
          updated.courier_vehicle_plate = courierDetails.plate;
        }

        if (newStatus === 'Completed') {
          updated.completed_at = new Date().toISOString();
        }

        return updated;
      })
    );

    // CRITICAL PRD RULE: Stocks change ONLY when status is 'Completed'!
    if (newStatus === 'Completed') {
      const targetTransfer = transfers.find((t) => t.id === transferId);
      if (targetTransfer) {
        setBatches((prevBatches) => {
          return prevBatches.map((b) => {
            // Deduct from sender batch
            if (b.id === targetTransfer.batch_id) {
              return {
                ...b,
                stock: Math.max(0, b.stock - targetTransfer.qty),
                updated_at: new Date().toISOString(),
              };
            }
            return b;
          });
        });

        // Add to receiver batch (or create new batch entry if not exists)
        setBatches((prevBatches) => {
          const sourceBatch = prevBatches.find((b) => b.id === targetTransfer.batch_id);
          if (!sourceBatch) return prevBatches;

          const existingReceiverBatch = prevBatches.find(
            (b) =>
              b.facility_id === targetTransfer.to_facility_id &&
              b.drug_id === targetTransfer.drug_id &&
              b.batch_no === sourceBatch.batch_no
          );

          if (existingReceiverBatch) {
            return prevBatches.map((b) =>
              b.id === existingReceiverBatch.id
                ? { ...b, stock: b.stock + targetTransfer.qty, updated_at: new Date().toISOString() }
                : b
            );
          } else {
            const newReceiverBatch: DrugBatch = {
              id: `batch_recv_${Date.now()}`,
              facility_id: targetTransfer.to_facility_id,
              drug_id: targetTransfer.drug_id,
              batch_no: sourceBatch.batch_no,
              expiry_date: sourceBatch.expiry_date,
              stock: targetTransfer.qty,
              updated_at: new Date().toISOString(),
              source_supplier: `Redistribution from ${currentFacility.name}`,
            };
            return [...prevBatches, newReceiverBatch];
          }
        });
      }
    }
  };

  const updateScoreWeights = (weights: Partial<ScoreWeights>) => {
    setScoreWeights((prev) => ({ ...prev, ...weights }));
  };

  const verifyFacility = (facilityId: string, status: 'verified' | 'rejected' | 'pending') => {
    setFacilities((prev) =>
      prev.map((f) => (f.id === facilityId ? { ...f, verified_status: status } : f))
    );
  };

  const addDrug = (drug: DrugMaster) => {
    setDrugs((prev) => [...prev, drug]);
  };

  const resetDemoData = () => {
    setFacilities(INITIAL_FACILITIES);
    setDrugs(INITIAL_DRUGS);
    setBatches(INITIAL_BATCHES);
    setBurnRates(INITIAL_BURN_RATES);
    setTransfers(INITIAL_TRANSFERS);
    setScoreWeights(DEFAULT_SCORE_WEIGHTS);
    setCurrentRole('pharmacist');
    setCurrentFacilityId('fac_simpang');
    localStorage.removeItem(`${STORAGE_KEY_PREFIX}facilities`);
    localStorage.removeItem(`${STORAGE_KEY_PREFIX}drugs`);
    localStorage.removeItem(`${STORAGE_KEY_PREFIX}batches`);
    localStorage.removeItem(`${STORAGE_KEY_PREFIX}burnRates`);
    localStorage.removeItem(`${STORAGE_KEY_PREFIX}transfers`);
    localStorage.removeItem(`${STORAGE_KEY_PREFIX}weights`);
  };

  return (
    <AppContext.Provider
      value={{
        facilities,
        drugs,
        batches,
        burnRates,
        transfers,
        scoreWeights,
        currentRole,
        currentFacilityId,
        currentFacility,
        forecasts,
        impactMetrics,
        switchRole,
        addBatch,
        updateBatch,
        importBatches,
        createTransfer,
        updateTransferStatus,
        updateScoreWeights,
        verifyFacility,
        addDrug,
        resetDemoData,
      }}
    >
      {children}
    </AppContext.Provider>
  );
};

export const useApp = () => {
  const context = useContext(AppContext);
  if (!context) {
    throw new Error('useApp must be used within an AppProvider');
  }
  return context;
};
