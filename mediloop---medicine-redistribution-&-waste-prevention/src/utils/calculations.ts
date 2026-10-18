import { DrugBatch, ExpiryForecast, HealthFacility, MatchCandidate, RiskLevel, ScoreWeights } from '../types';

/**
 * Calculates Haversine distance between two coordinates in kilometers.
 */
export function calculateHaversineDistance(
  lat1: number,
  lon1: number,
  lat2: number,
  lon2: number
): number {
  const R = 6371; // Earth's radius in km
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  const distance = R * c;
  return Math.round(distance * 10) / 10; // 1 decimal place
}

/**
 * Computes the number of days until expiry from today.
 */
export function getDaysToExpiry(expiryDateStr: string, referenceDate: Date = new Date()): number {
  const expDate = new Date(expiryDateStr);
  const diffTime = expDate.getTime() - referenceDate.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return Math.max(0, diffDays);
}

/**
 * Computes expiry forecast for a given batch based on burn rate and expiry date.
 */
export function computeBatchForecast(
  batch: DrugBatch,
  burnRate: number,
  historyDaysCount: number = 30,
  referenceDate: Date = new Date()
): ExpiryForecast {
  const effectiveBurnRate = Math.max(0.1, burnRate);
  const daysToDeplete = Math.round(batch.stock / effectiveBurnRate);
  const daysToExpiry = getDaysToExpiry(batch.expiry_date, referenceDate);
  
  // Projected waste formula from PRD: max(0, stock - burn_rate * days_to_expiry)
  const projectedWaste = Math.max(0, Math.round(batch.stock - effectiveBurnRate * daysToExpiry));
  const wasteRatio = batch.stock > 0 ? projectedWaste / batch.stock : 0;

  let riskLevel: RiskLevel = 'SAFE';
  if (wasteRatio >= 0.5) {
    riskLevel = 'HIGH';
  } else if (wasteRatio >= 0.2) {
    riskLevel = 'MEDIUM';
  } else if (wasteRatio > 0) {
    riskLevel = 'WATCH';
  } else {
    riskLevel = 'SAFE';
  }

  return {
    batch_id: batch.id,
    facility_id: batch.facility_id,
    drug_id: batch.drug_id,
    burn_rate: Math.round(effectiveBurnRate * 10) / 10,
    days_to_deplete: daysToDeplete,
    days_to_expiry: daysToExpiry,
    projected_waste: projectedWaste,
    waste_ratio: Math.round(wasteRatio * 100) / 100,
    risk_level: riskLevel,
    computed_at: new Date().toISOString(),
    is_limited_data: batch.is_limited_data !== undefined ? batch.is_limited_data : historyDaysCount < 14,
  };
}

/**
 * Computes matching recommendations for an at-risk batch across available candidate facilities.
 */
export function computeMatchesForBatch(
  sourceFacility: HealthFacility,
  batch: DrugBatch,
  forecast: ExpiryForecast,
  allFacilities: HealthFacility[],
  allBatches: DrugBatch[],
  facilityBurnRates: Record<string, number>, // keyed by facilityId_drugId
  weights: ScoreWeights
): MatchCandidate[] {
  if (forecast.projected_waste <= 0) {
    return [];
  }

  const candidates: MatchCandidate[] = [];

  for (const targetFacility of allFacilities) {
    // Cannot match with self or unverified facilities
    if (targetFacility.id === sourceFacility.id || targetFacility.verified_status !== 'verified') {
      continue;
    }

    // Don't route to district unit as recipient consumer (district unit is admin/warehouse)
    if (targetFacility.type === 'District Unit') {
      continue;
    }

    const distanceKm = calculateHaversineDistance(
      sourceFacility.lat,
      sourceFacility.lng,
      targetFacility.lat,
      targetFacility.lng
    );

    // Find target facility current stock for the same drug
    const targetBatches = allBatches.filter(
      (b) => b.facility_id === targetFacility.id && b.drug_id === batch.drug_id
    );
    const targetStock = targetBatches.reduce((acc, b) => acc + b.stock, 0);

    const burnRateKey = `${targetFacility.id}_${batch.drug_id}`;
    const targetBurnRate = facilityBurnRates[burnRateKey] || 3.5;

    // Standard days to regular delivery cycle (default 30 days)
    const daysToNextRegularDelivery = 30;
    // Need formula: (burn_rate * days_to_regular_cycle) - current_stock
    const estimatedNeed = Math.max(0, Math.round(targetBurnRate * daysToNextRegularDelivery - targetStock));

    // Feasibility rule: Need > 0 and target facility has sufficient burn rate to use the batch before it expires
    const targetDaysToDepleteStock = targetStock > 0 ? targetStock / targetBurnRate : 0;
    const shelfLifeIsOk = forecast.days_to_expiry > targetDaysToDepleteStock;

    if (estimatedNeed > 0 && shelfLifeIsOk) {
      // 1. Shortage Score (0 - 100): severity of stock depletion
      const stockRatio = targetStock / Math.max(1, targetBurnRate * 15);
      const shortageScore = Math.max(10, Math.min(100, Math.round((1 - Math.min(1, stockRatio)) * 100)));

      // 2. Distance Score (0 - 100): closer is better (100 at 0km, degrades by ~4 points per km)
      const distanceScore = Math.max(15, Math.min(100, Math.round(100 - distanceKm * 4.5)));

      // 3. Shelf-life compatibility score (0 - 100): how safely the receiver can consume the transferred batch
      const bufferDays = forecast.days_to_expiry - targetDaysToDepleteStock;
      const shelfLifeScore = Math.max(20, Math.min(100, Math.round(Math.min(1, bufferDays / 45) * 100)));

      // Weighted score
      const finalScore = Math.round(
        weights.shortage_weight * shortageScore +
        weights.distance_weight * distanceScore +
        weights.shelf_life_weight * shelfLifeScore
      );

      // Transfer allocation: cannot exceed source projected waste, cannot exceed target estimated need
      const recommendedQty = Math.min(forecast.projected_waste, estimatedNeed);

      let reason = '';
      if (shortageScore >= 80) {
        reason = `Critical stock level (${targetStock} units left, runs out in ${Math.round(targetDaysToDepleteStock)} days), close distance (${distanceKm} km).`;
      } else if (distanceKm < 6) {
        reason = `High geographic proximity (${distanceKm} km), estimated need of ${estimatedNeed} units before regular supply cycle.`;
      } else {
        reason = `Stable consumption velocity (${targetBurnRate}/day) capable of fully dispensing ${recommendedQty} units before expiry.`;
      }

      candidates.push({
        candidate_facility: targetFacility,
        distance_km: distanceKm,
        receiver_current_stock: targetStock,
        receiver_burn_rate: targetBurnRate,
        estimated_need: estimatedNeed,
        score: finalScore,
        score_breakdown: {
          shortage_score: shortageScore,
          distance_score: distanceScore,
          shelf_life_score: shelfLifeScore,
        },
        recommended_transfer_qty: recommendedQty,
        reason,
      });
    }
  }

  // Sort by highest score first
  return candidates.sort((a, b) => b.score - a.score);
}

/**
 * Format IDR currency in standard English notation (e.g. "Rp 45,280,000")
 */
export function formatIDR(amount: number): string {
  return `Rp ${Math.round(amount).toLocaleString('en-US')}`;
}

