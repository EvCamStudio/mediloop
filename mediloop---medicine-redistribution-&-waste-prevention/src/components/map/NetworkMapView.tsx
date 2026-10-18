import React, { useEffect, useRef, useState, useMemo } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useApp } from '../../context/AppContext';
import { HealthFacility, DrugBatch } from '../../types';
import { calculateHaversineDistance, computeBatchForecast } from '../../utils/calculations';
import {
  MapPin,
  Filter,
  Navigation,
  AlertTriangle,
  Building2,
  CheckCircle2,
  Layers,
  ArrowRight,
  TrendingDown,
  Sparkles,
  Info,
} from 'lucide-react';

interface NetworkMapViewProps {
  onSelectBatchMatch?: (batchId: string) => void;
  selectedBatchId?: string | null;
}

export const NetworkMapView: React.FC<NetworkMapViewProps> = ({ onSelectBatchMatch, selectedBatchId }) => {
  const { facilities, batches, drugs, burnRates, currentFacility } = useApp();

  const mapContainerRef = useRef<HTMLDivElement>(null);
  const mapInstanceRef = useRef<L.Map | null>(null);
  const layerGroupRef = useRef<L.LayerGroup | null>(null);

  const [selectedFacility, setSelectedFacility] = useState<HealthFacility | null>(currentFacility);
  const [filterDrugId, setFilterDrugId] = useState<string>('all');
  const [filterRadiusKm, setFilterRadiusKm] = useState<number>(25);
  const [onlyAtRisk, setOnlyAtRisk] = useState<boolean>(false);

  // Compute status for all facilities (surplus vs shortage)
  const facilityStatusMap = useMemo(() => {
    const map: Record<
      string,
      {
        hasHighRisk: boolean;
        hasMediumRisk: boolean;
        hasShortage: boolean;
        atRiskBatches: DrugBatch[];
        shortageDrugs: string[];
      }
    > = {};

    facilities.forEach((fac) => {
      const facBatches = batches.filter((b) => b.facility_id === fac.id);
      let highRisk = false;
      let medRisk = false;
      const atRisk: DrugBatch[] = [];
      const shortage: string[] = [];

      facBatches.forEach((b) => {
        const rateKey = `${fac.id}_${b.drug_id}`;
        const burnRate = burnRates[rateKey] || 2.0;
        const forecast = computeBatchForecast(b, burnRate, 30);
        if (forecast.risk_level === 'HIGH') {
          highRisk = true;
          atRisk.push(b);
        } else if (forecast.risk_level === 'MEDIUM') {
          medRisk = true;
          atRisk.push(b);
        }
      });

      // Deficit simulation: check if certain core drugs are low or missing
      drugs.forEach((d) => {
        const hasDrug = facBatches.some((b) => b.drug_id === d.id && b.stock > 30);
        if (!hasDrug && fac.type !== 'District Unit') {
          shortage.push(d.name);
        }
      });

      map[fac.id] = {
        hasHighRisk: highRisk,
        hasMediumRisk: medRisk,
        hasShortage: shortage.length > 0,
        atRiskBatches: atRisk,
        shortageDrugs: shortage,
      };
    });

    return map;
  }, [facilities, batches, drugs, burnRates]);

  // Filter facilities
  const filteredFacilities = useMemo(() => {
    return facilities.filter((fac) => {
      // Radius filter from active user facility
      const dist = calculateHaversineDistance(
        currentFacility.lat,
        currentFacility.lng,
        fac.lat,
        fac.lng
      );
      if (dist > filterRadiusKm) return false;

      // Only at risk
      if (onlyAtRisk) {
        const stat = facilityStatusMap[fac.id];
        if (!stat?.hasHighRisk && !stat?.hasMediumRisk && !stat?.hasShortage) {
          return false;
        }
      }

      // Filter by drug
      if (filterDrugId !== 'all') {
        const hasThisDrug = batches.some(
          (b) => b.facility_id === fac.id && b.drug_id === filterDrugId
        );
        if (!hasThisDrug && fac.type !== 'District Unit') return false;
      }

      return true;
    });
  }, [facilities, currentFacility, filterRadiusKm, onlyAtRisk, filterDrugId, batches, facilityStatusMap]);

  // Initialize Leaflet Map
  useEffect(() => {
    if (!mapContainerRef.current) return;

    if (!mapInstanceRef.current) {
      // Center on Medan, North Sumatra
      const map = L.map(mapContainerRef.current, {
        center: [currentFacility.lat, currentFacility.lng],
        zoom: 12,
        zoomControl: false,
      });

      // Standard OpenStreetMap TileLayer (Open Source & Reliable)
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
      }).addTo(map);

      L.control.zoom({ position: 'bottomright' }).addTo(map);

      const layerGroup = L.layerGroup().addTo(map);
      layerGroupRef.current = layerGroup;
      mapInstanceRef.current = map;
    }

    return () => {
      if (mapInstanceRef.current) {
        mapInstanceRef.current.remove();
        mapInstanceRef.current = null;
      }
    };
  }, []);

  // Update Markers & Geodesic Paths whenever filters or facilities change
  useEffect(() => {
    if (!mapInstanceRef.current || !layerGroupRef.current) return;

    const layerGroup = layerGroupRef.current;
    layerGroup.clearLayers();

    filteredFacilities.forEach((fac) => {
      const isSelected = selectedFacility?.id === fac.id;
      const isCurrent = currentFacility.id === fac.id;
      const stat = facilityStatusMap[fac.id] || {
        hasHighRisk: false,
        hasMediumRisk: false,
        hasShortage: false,
      };

      let pinColor = '#1D4ED8'; // Primary Blue: Safe
      let pinLabel = 'SAFE';
      let pinEmoji = '🏥';

      if (fac.type === 'District Unit') {
        pinColor = '#0B1F4B'; // Navy: District Authority
        pinLabel = 'DISTRICT';
        pinEmoji = '🏛️';
      } else if (stat.hasHighRisk) {
        pinColor = '#D9480F'; // Risk Orange: Surplus At Risk
        pinLabel = 'AT RISK';
        pinEmoji = '⚠️';
      } else if (stat.hasShortage) {
        pinColor = '#B7791F'; // Amber: Stock Deficit
        pinLabel = 'DEFICIT';
        pinEmoji = '📉';
      }

      const iconHtml = `
        <div style="position: relative; display: flex; align-items: center; justify-content: center; font-family: system-ui, sans-serif;">
          <div style="
            background: ${pinColor};
            color: white;
            padding: 5px 10px;
            border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(11, 31, 75, 0.28);
            border: 2px solid white;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            transform: ${isSelected ? 'scale(1.18)' : 'scale(1)'};
            transition: all 0.2s ease;
          ">
            <span>${pinEmoji}</span>
            <span>${fac.name.replace('Puskesmas ', '').replace('Klinik Pratama ', '')}</span>
          </div>
          ${
            isCurrent
              ? `<div style="position: absolute; top: -10px; right: -6px; background: #0B1F4B; color: white; border-radius: 9999px; font-size: 9px; padding: 1px 6px; font-weight: 900; border: 1.5px solid white;">YOU</div>`
              : ''
          }
        </div>
      `;

      const customIcon = L.divIcon({
        className: 'custom-leaflet-marker',
        html: iconHtml,
        iconSize: [120, 36],
        iconAnchor: [60, 18],
      });

      const marker = L.marker([fac.lat, fac.lng], { icon: customIcon }).addTo(layerGroup);

      marker.on('click', () => {
        setSelectedFacility(fac);
      });
    });

    // Draw geodesic dashed lines from the selected facility (if it has surplus) to potential shortage receivers
    if (selectedFacility) {
      const selectedStat = facilityStatusMap[selectedFacility.id];
      if (selectedStat?.hasHighRisk || selectedStat?.hasMediumRisk || selectedFacility.id === 'fac_simpang') {
        filteredFacilities.forEach((target) => {
          if (target.id === selectedFacility.id) return;

          const targetStat = facilityStatusMap[target.id];
          if (targetStat?.hasShortage || target.id === 'fac_medan_baru' || target.id === 'fac_sentosa') {
            const distance = calculateHaversineDistance(
              selectedFacility.lat,
              selectedFacility.lng,
              target.lat,
              target.lng
            );

            const latlngs: [number, number][] = [
              [selectedFacility.lat, selectedFacility.lng],
              [target.lat, target.lng],
            ];

            L.polyline(latlngs, {
              color: '#1D4ED8',
              weight: 2.5,
              dashArray: '6, 8',
              opacity: 0.85,
            }).addTo(layerGroup);

            const midLat = (selectedFacility.lat + target.lat) / 2;
            const midLng = (selectedFacility.lng + target.lng) / 2;

            const distIcon = L.divIcon({
              className: 'dist-label',
              html: `<div style="background: white; border: 1.5px solid #1D4ED8; color: #1D4ED8; font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; box-shadow: 0 2px 8px rgba(11,31,75,0.15);">${distance} km</div>`,
              iconSize: [54, 20],
              iconAnchor: [27, 10],
            });

            L.marker([midLat, midLng], { icon: distIcon }).addTo(layerGroup);
          }
        });
      }
    }
  }, [filteredFacilities, selectedFacility, currentFacility, facilityStatusMap]);

  // Center on selected facility
  const handleFlyTo = (fac: HealthFacility) => {
    setSelectedFacility(fac);
    if (mapInstanceRef.current) {
      mapInstanceRef.current.flyTo([fac.lat, fac.lng], 14, { duration: 1.2 });
    }
  };

  return (
    <div className="space-y-4 font-body text-slate-800">
      {/* Top Filter Bar */}
      <div className="bg-white p-4 rounded-2xl border border-[#DBE5F5] shadow-xs flex flex-wrap items-center justify-between gap-3 text-sm">
        <div className="flex flex-wrap items-center gap-3">
          <div className="flex items-center gap-2 text-xs font-bold text-[#0B1F4B]">
            <Filter className="w-4 h-4 text-[#1D4ED8]" />
            <span>Map Filters:</span>
          </div>

          {/* Drug Filter */}
          <select
            value={filterDrugId}
            onChange={(e) => setFilterDrugId(e.target.value)}
            className="text-xs bg-[#F8FAFC] border border-[#DBE5F5] rounded-xl px-3 py-2 text-[#0B1F4B] font-medium outline-none"
          >
            <option value="all">All Medicine Types</option>
            {drugs.map((d) => (
              <option key={d.id} value={d.id}>
                {d.name}
              </option>
            ))}
          </select>

          {/* Radius Filter */}
          <div className="flex items-center gap-2 text-xs text-[#52627F] bg-[#F8FAFC] border border-[#DBE5F5] px-3 py-1.5 rounded-xl">
            <span>Radius:</span>
            <input
              type="range"
              min="5"
              max="35"
              value={filterRadiusKm}
              onChange={(e) => setFilterRadiusKm(Number(e.target.value))}
              className="w-20 accent-[#1D4ED8] cursor-pointer"
            />
            <span className="font-bold text-[#1D4ED8]">{filterRadiusKm} km</span>
          </div>

          {/* Only At Risk Checkbox */}
          <label className="flex items-center gap-2 text-xs font-semibold text-[#0B1F4B] cursor-pointer bg-[#F8FAFC] border border-[#DBE5F5] px-3 py-1.5 rounded-xl hover:bg-[#EAF1FF]">
            <input
              type="checkbox"
              checked={onlyAtRisk}
              onChange={(e) => setOnlyAtRisk(e.target.checked)}
              className="rounded text-[#1D4ED8] accent-[#1D4ED8]"
            />
            <span>Only Surplus / Deficit Facilities</span>
          </label>
        </div>

        {/* Legend */}
        <div className="flex items-center gap-4 text-xs font-semibold text-[#52627F]">
          <div className="flex items-center gap-1.5">
            <span className="w-3 h-3 rounded-full bg-[#D9480F]"></span>
            <span className="text-[#0B1F4B]">Surplus (At Risk)</span>
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-3 h-3 rounded-full bg-[#B7791F]"></span>
            <span className="text-[#0B1F4B]">Stock Deficit</span>
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-3 h-3 rounded-full bg-[#1D4ED8]"></span>
            <span className="text-[#0B1F4B]">Safe Buffer</span>
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-3 h-3 rounded-full bg-[#0B1F4B]"></span>
            <span className="text-[#0B1F4B]">District Authority</span>
          </div>
        </div>
      </div>

      {/* Main Map + Side Inspection Panel */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {/* Map Container */}
        <div className="lg:col-span-2 relative h-[540px] rounded-2xl border border-[#DBE5F5] overflow-hidden shadow-xs bg-[#F8FAFC]">
          <div ref={mapContainerRef} className="w-full h-full" />

          {/* Floating Monitored Badge */}
          <div className="absolute top-4 left-4 z-[400] bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-xl shadow-sm border border-[#DBE5F5] text-xs flex items-center gap-2">
            <MapPin className="w-4 h-4 text-[#1D4ED8] shrink-0" />
            <div>
              <p className="font-bold text-[#0B1F4B]">{filteredFacilities.length} Facilities Monitored</p>
              <p className="text-[11px] text-[#52627F]">Medan Metropolitan Healthcare Logistics Topology</p>
            </div>
          </div>
        </div>

        {/* Side Panel: Selected Facility Detail & Matches */}
        <div className="bg-white rounded-2xl border border-[#DBE5F5] p-5 shadow-xs flex flex-col justify-between">
          {selectedFacility ? (
            <div className="space-y-4">
              <div className="flex items-start justify-between border-b border-[#DBE5F5] pb-3">
                <div>
                  <div className="flex items-center gap-2">
                    <span className="px-2 py-0.5 text-[10px] font-bold rounded-md bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] uppercase">
                      {selectedFacility.type}
                    </span>
                    {selectedFacility.id === currentFacility.id && (
                      <span className="px-2 py-0.5 text-[10px] font-bold rounded-md bg-[#0B1F4B] text-white">
                        Your Facility
                      </span>
                    )}
                  </div>
                  <h3 className="text-base font-bold text-[#0B1F4B] mt-1">{selectedFacility.name}</h3>
                  <p className="text-xs text-[#52627F] flex items-center gap-1 mt-0.5">
                    <MapPin className="w-3.5 h-3.5 text-[#52627F]" />
                    {selectedFacility.address}
                  </p>
                </div>
                <button
                  onClick={() => handleFlyTo(selectedFacility)}
                  className="p-2 rounded-xl bg-[#F8FAFC] hover:bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5] transition-colors cursor-pointer"
                  title="Focus map on this facility"
                >
                  <Navigation className="w-4 h-4" />
                </button>
              </div>

              {/* Distance from current facility */}
              {selectedFacility.id !== currentFacility.id && (
                <div className="bg-[#EAF1FF] border border-[#DBE5F5] rounded-xl p-3 flex items-center justify-between text-xs">
                  <span className="text-[#52627F] font-semibold">Distance from your facility:</span>
                  <span className="font-extrabold text-[#1D4ED8] text-sm">
                    {calculateHaversineDistance(
                      currentFacility.lat,
                      currentFacility.lng,
                      selectedFacility.lat,
                      selectedFacility.lng
                    )}{' '}
                    km (Haversine)
                  </span>
                </div>
              )}

              {/* Status & At-Risk Batches Overview */}
              <div>
                <h4 className="text-xs font-bold text-[#0B1F4B] uppercase tracking-wider mb-2">
                  Pharmaceutical Stock Status
                </h4>

                {facilityStatusMap[selectedFacility.id]?.atRiskBatches.length > 0 ? (
                  <div className="space-y-2">
                    <div className="p-3 rounded-xl bg-[#F8FAFC] border border-[#D9480F]/30">
                      <div className="flex items-center gap-2 text-xs font-bold text-[#D9480F]">
                        <AlertTriangle className="w-4 h-4 text-[#D9480F]" />
                        <span>
                          {facilityStatusMap[selectedFacility.id].atRiskBatches.length} Surplus At-Risk Batches
                        </span>
                      </div>
                      <div className="mt-2 space-y-1.5">
                        {facilityStatusMap[selectedFacility.id].atRiskBatches.map((b) => {
                          const drug = drugs.find((d) => d.id === b.drug_id);
                          return (
                            <div
                              key={b.id}
                              className="bg-white p-2.5 rounded-lg border border-[#DBE5F5] text-xs flex items-center justify-between"
                            >
                              <div>
                                <p className="font-bold text-[#0B1F4B]">{drug?.name}</p>
                                <p className="text-[11px] text-[#52627F]">
                                  Batch #{b.batch_no} • Stock: {b.stock} boxes
                                </p>
                              </div>
                              {onSelectBatchMatch && (
                                <button
                                  onClick={() => onSelectBatchMatch(b.id)}
                                  className="px-2.5 py-1 bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold rounded text-[11px] transition-colors cursor-pointer"
                                >
                                  Find Match
                                </button>
                              )}
                            </div>
                          );
                        })}
                      </div>
                    </div>
                  </div>
                ) : (
                  <div className="p-3 rounded-xl bg-[#EAF1FF] border border-[#DBE5F5] text-xs text-[#1D4ED8] font-bold flex items-center gap-2">
                    <CheckCircle2 className="w-4 h-4 text-[#1D4ED8]" />
                    <span>All stock batches within safe shelf-life margins.</span>
                  </div>
                )}

                {/* Shortage indicator */}
                {facilityStatusMap[selectedFacility.id]?.hasShortage && (
                  <div className="mt-2 p-3 rounded-xl bg-[#F8FAFC] border border-[#B7791F]/40 text-xs text-[#0B1F4B]">
                    <p className="font-bold text-[#B7791F] flex items-center gap-1.5">
                      <span>📉</span> Stock Deficit / Needs Supply:
                    </p>
                    <p className="text-[11px] mt-1 text-[#52627F]">
                      {facilityStatusMap[selectedFacility.id].shortageDrugs.slice(0, 3).join(', ')}
                    </p>
                  </div>
                )}
              </div>

              {/* Contact info */}
              <div className="text-xs text-[#52627F] pt-2 border-t border-[#DBE5F5] space-y-1">
                <p>
                  <span className="font-semibold text-[#0B1F4B]">Lead Pharmacist:</span>{' '}
                  {selectedFacility.pic_name}
                </p>
                <p>
                  <span className="font-semibold text-[#0B1F4B]">Official Contact:</span>{' '}
                  {selectedFacility.pic_phone}
                </p>
                <p>
                  <span className="font-semibold text-[#0B1F4B]">Facility License:</span>{' '}
                  <span className="font-mono text-[#0B1F4B]">{selectedFacility.license_number}</span>
                </p>
              </div>
            </div>
          ) : (
            <div className="py-20 text-center text-[#52627F]">
              <Building2 className="w-10 h-10 mx-auto mb-2 opacity-50 text-[#1D4ED8]" />
              <p className="text-xs">Click any facility pin on the map to inspect stock inventory & profile.</p>
            </div>
          )}

          {/* Quick Switch List */}
          <div className="mt-4 pt-3 border-t border-[#DBE5F5]">
            <p className="text-[11px] font-bold text-[#52627F] uppercase tracking-wider mb-2">
              Network Healthcare Facilities ({facilities.length})
            </p>
            <div className="max-h-36 overflow-y-auto space-y-1 pr-1 text-xs">
              {facilities.map((fac) => (
                <button
                  key={fac.id}
                  onClick={() => handleFlyTo(fac)}
                  className={`w-full text-left px-2.5 py-1.5 rounded-lg text-xs flex items-center justify-between transition-colors cursor-pointer ${
                    selectedFacility?.id === fac.id
                      ? 'bg-[#EAF1FF] text-[#1D4ED8] font-bold border border-[#DBE5F5]'
                      : 'hover:bg-[#F8FAFC] text-[#0B1F4B]'
                  }`}
                >
                  <span className="truncate">{fac.name}</span>
                  <span className="text-[10px] text-[#52627F] shrink-0 ml-2 font-mono">
                    {calculateHaversineDistance(currentFacility.lat, currentFacility.lng, fac.lat, fac.lng)} km
                  </span>
                </button>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
