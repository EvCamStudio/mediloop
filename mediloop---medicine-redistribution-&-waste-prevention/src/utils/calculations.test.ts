import { test, describe } from 'node:test';
import assert from 'node:assert/strict';
import {
  computeBatchForecast,
  calculateHaversineDistance,
  computeMatchesForBatch,
} from './calculations.js';
import { DrugBatch, HealthFacility, ScoreWeights } from '../types/index.js';

describe('MediLoop Calculation & Prediction Logic', () => {
  test('correctly calculates forecast when stock runs out after expiry (High Risk)', () => {
    // 480 boxes, burn rate 4/day, expiry in 45 days -> 180 boxes consumed, 300 boxes waste
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + 45);
    const dateStr = targetDate.toISOString().split('T')[0];

    const batch: DrugBatch = {
      id: 'b1',
      facility_id: 'fac1',
      drug_id: 'd1',
      batch_no: 'TEST-001',
      expiry_date: dateStr,
      stock: 480,
      updated_at: new Date().toISOString(),
    };

    const forecast = computeBatchForecast(batch, 4.0, 30);
    assert.strictEqual(forecast.days_to_expiry, 45);
    assert.strictEqual(forecast.days_to_deplete, 120);
    assert.strictEqual(forecast.projected_waste, 300);
    assert.strictEqual(forecast.waste_ratio > 0.5, true);
    assert.strictEqual(forecast.risk_level, 'HIGH');
  });

  test('correctly identifies safe batches when stock depletes before expiry', () => {
    // 100 boxes, burn rate 5/day -> 20 days to deplete. Expiry in 60 days
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + 60);
    const dateStr = targetDate.toISOString().split('T')[0];

    const batch: DrugBatch = {
      id: 'b2',
      facility_id: 'fac1',
      drug_id: 'd1',
      batch_no: 'TEST-SAFE',
      expiry_date: dateStr,
      stock: 100,
      updated_at: new Date().toISOString(),
    };

    const forecast = computeBatchForecast(batch, 5.0, 30);
    assert.strictEqual(forecast.days_to_deplete, 20);
    assert.strictEqual(forecast.projected_waste, 0);
    assert.strictEqual(forecast.risk_level, 'SAFE');
  });

  test('calculates accurate Haversine distance between coordinates', () => {
    // Distance between Medan Puskesmas Simpang (3.5701, 98.6658) and Puskesmas Medan Baru (3.5782, 98.6601)
    const dist = calculateHaversineDistance(3.5701, 98.6658, 3.5782, 98.6601);
    assert.ok(dist > 0.8 && dist < 1.5, `Distance was ${dist} km, expected ~1 km`);
  });
});
