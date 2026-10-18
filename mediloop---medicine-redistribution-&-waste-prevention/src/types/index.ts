export type UserRole = 'pharmacist' | 'facility_head' | 'district_unit' | 'receiver' | 'admin';

export type RiskLevel = 'HIGH' | 'MEDIUM' | 'WATCH' | 'SAFE';

export type TransferStatus =
  | 'Requested'
  | 'Pharmacist Approved'
  | 'District Approved'
  | 'Handed Over'
  | 'Completed'
  | 'Rejected'
  | 'Cancelled';

export interface HealthFacility {
  id: string;
  name: string;
  type: 'Puskesmas' | 'Klinik' | 'RSUD' | 'District Unit';
  address: string;
  city: string;
  lat: number;
  lng: number;
  verified_status: 'verified' | 'pending' | 'rejected';
  license_number: string;
  pic_name: string;
  pic_phone: string;
  district_code: string;
}

export interface DrugMaster {
  id: string;
  name: string;
  generic_name: string;
  form: 'Tablet' | 'Capsule' | 'Syrup' | 'Injection' | 'Ointment';
  strength: string;
  category: 'Antibiotics' | 'Analgesics' | 'Cardiovascular' | 'Antidiabetic' | 'Cold Chain (Vaccine/Insulin)' | 'Fluids & Electrolytes';
  cold_chain: boolean;
  unit_price: number; // in IDR (Rupiah)
  min_stock_alert: number;
}

export interface DrugBatch {
  id: string;
  facility_id: string;
  drug_id: string;
  batch_no: string;
  expiry_date: string; // YYYY-MM-DD
  stock: number; // in units/boxes
  updated_at: string;
  source_supplier?: string;
  is_limited_data?: boolean;
}

export interface DailyUsage {
  id: string;
  batch_id: string;
  facility_id: string;
  drug_id: string;
  date: string;
  quantity_used: number;
}

export interface ExpiryForecast {
  batch_id: string;
  facility_id: string;
  drug_id: string;
  burn_rate: number; // units per day
  days_to_deplete: number;
  days_to_expiry: number;
  projected_waste: number;
  waste_ratio: number;
  risk_level: RiskLevel;
  computed_at: string;
  is_limited_data?: boolean;
}

export interface MatchCandidate {
  candidate_facility: HealthFacility;
  distance_km: number;
  receiver_current_stock: number;
  receiver_burn_rate: number;
  estimated_need: number;
  score: number; // 0 - 100
  score_breakdown: {
    shortage_score: number; // 50%
    distance_score: number; // 30%
    shelf_life_score: number; // 20%
  };
  recommended_transfer_qty: number;
  reason: string;
}

export interface TransferRequest {
  id: string;
  transfer_code: string; // e.g. TRF-2026-001
  batch_id: string;
  drug_id: string;
  from_facility_id: string;
  to_facility_id: string;
  qty: number;
  status: TransferStatus;
  created_at: string;
  updated_at: string;
  completed_at?: string;
  rejection_reason?: string;
  notes?: string;
  courier_name?: string;
  courier_vehicle_plate?: string;
  handover_document_number?: string;
  history: TransferEvent[];
}

export interface TransferEvent {
  id: string;
  transfer_id: string;
  actor_name: string;
  actor_role: string;
  action: string;
  note?: string;
  timestamp: string;
}

export interface ScoreWeights {
  shortage_weight: number; // default 0.50
  distance_weight: number; // default 0.30
  shelf_life_weight: number; // default 0.20
  forecast_window_days: number; // default 30
}

export interface ImpactMetrics {
  total_units_saved: number;
  total_value_saved_idr: number;
  co2_waste_prevented_kg: number;
  stock_out_days_prevented: number;
  facilities_assisted: number;
  transfers_completed: number;
}
