@extends('layouts.mediloop', ['title' => 'Peta Distribusi & Jejaring Logistik — MediLoop'])

@section('content')
<div class="space-y-4">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-[#DBE5F5] gap-3">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#0B1F4B]">Peta Jejaring Logistik &amp; Faskes</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-blue-100 text-[#1D4ED8]">
                    {{ $userFacility?->name ?? 'Klaster Faskes Regional' }}
                </span>
            </div>
            <p class="text-xs text-[#52627F] mt-0.5">
                Pemetaan spasial faskes, kapabilitas rantai dingin (*cold-chain*), dan visualisasi rute redistribusi obat aktif.
            </p>
        </div>

        <!-- Quick Summary Stats Pills -->
        <div class="flex items-center flex-wrap gap-2 text-xs">
            <span class="px-3 py-1.5 rounded-lg bg-white border border-[#DBE5F5] font-semibold text-[#0B1F4B] shadow-2xs">
                🏥 <b class="text-[#1D4ED8]">{{ $facilityStats['total'] }}</b> Faskes Terhubung
            </span>
            <span class="px-3 py-1.5 rounded-lg bg-white border border-[#DBE5F5] font-semibold text-[#0B1F4B] shadow-2xs">
                ❄️ <b class="text-cyan-700">{{ $facilityStats['cold_chain'] }}</b> Cold-Chain Ready
            </span>
            <span class="px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-200 font-semibold text-[#1D4ED8] shadow-2xs">
                🔄 <b class="text-[#1D4ED8]">{{ $facilityStats['active_routes'] }}</b> Rute Redistribusi
            </span>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#DBE5F5] shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <form method="GET" action="{{ route('map.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="font-bold text-[#52627F]">Tipe Faskes:</span>
                <select name="type" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-[#DBE5F5] rounded-lg text-xs font-semibold text-[#0B1F4B] focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <option value="">Semua Tipe Faskes</option>
                    <option value="RSUD" {{ $currentType === 'RSUD' ? 'selected' : '' }}>RSUD (Rumah Sakit Umum Daerah)</option>
                    <option value="PUSKESMAS" {{ $currentType === 'PUSKESMAS' ? 'selected' : '' }}>Puskesmas Kecamatan</option>
                    <option value="KLINIK" {{ $currentType === 'KLINIK' ? 'selected' : '' }}>Klinik Pratama</option>
                    <option value="RS_SWASTA" {{ $currentType === 'RS_SWASTA' ? 'selected' : '' }}>RS Swasta</option>
                </select>
            </div>

            <label class="inline-flex items-center gap-2 cursor-pointer font-semibold text-[#0B1F4B]">
                <input type="checkbox" name="cold_chain_only" value="1" onchange="this.form.submit()" {{ $coldChainOnly ? 'checked' : '' }} class="rounded border-slate-300 text-[#1D4ED8] focus:ring-[#1D4ED8]">
                <span>Hanya Faskes Cold Chain (2-8°C)</span>
            </label>
        </form>

        <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#1D4ED8]"></span> RSUD</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#0D9488]"></span> Puskesmas</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#10B981]"></span> Klinik</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-0.5 bg-blue-600 border-t-2 border-dashed border-blue-600"></span> Jalur Redistribusi</span>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN: Map Canvas (Left 70%) + Facility List Drawer (Right 30%) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        <!-- MAP CANVAS -->
        <div class="lg:col-span-8 bg-white rounded-xl border border-[#DBE5F5] shadow-xs overflow-hidden relative" style="height: 620px;">
            <div id="full-map" class="w-full h-full"></div>
            
            <!-- Map Legend Box floating in bottom left -->
            <div class="absolute bottom-4 left-4 z-[400] bg-white/95 backdrop-blur-xs p-3 rounded-xl border border-[#DBE5F5] shadow-md text-[11px] space-y-1.5 max-w-[220px]">
                <div class="font-bold text-[#0B1F4B] text-xs pb-1 border-b border-slate-100">Keterangan Peta</div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>Titik Lokasi Faskes:</span>
                    <span class="font-bold text-[#0B1F4B]">{{ count($facilities) }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>Rute Antarfaskes:</span>
                    <span class="font-bold text-blue-700">{{ count($transfers) }} jalur</span>
                </div>
                <div class="text-[10px] text-slate-400 pt-1">
                    *Klik penanda faskes untuk detail stok &amp; jarak.
                </div>
            </div>
        </div>

        <!-- FACILITY CARDS LIST -->
        <div class="lg:col-span-4 bg-white rounded-xl border border-[#DBE5F5] shadow-xs flex flex-col" style="height: 620px;">
            <div class="p-3.5 border-b border-[#DBE5F5] flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-[#0B1F4B]">Daftar Faskes Jejaring</h2>
                    <p class="text-[11px] text-[#52627F]">Klik faskes untuk memusatkan peta</p>
                </div>
                <span class="text-xs font-bold text-blue-700">{{ count($facilities) }} Faskes</span>
            </div>

            <div class="overflow-y-auto p-3 space-y-2.5 flex-1">
                @forelse($facilities as $f)
                    <div onclick="focusFacility({{ $f['latitude'] }}, {{ $f['longitude'] }}, '{{ addslashes($f['name']) }}')" class="p-3 rounded-xl border border-[#DBE5F5] hover:border-[#1D4ED8] hover:bg-blue-50/30 transition cursor-pointer text-xs space-y-1.5">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-bold text-[#0B1F4B] leading-tight">{{ $f['name'] }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $f['type'] === 'RSUD' ? 'bg-blue-100 text-blue-800' : ($f['type'] === 'PUSKESMAS' ? 'bg-teal-100 text-teal-800' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ $f['type'] }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 line-clamp-1">{{ $f['address'] }}</p>
                        
                        <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-100">
                            <span class="font-semibold text-slate-600">
                                @if($f['distance_from_user_km'] !== null)
                                    📍 <b>{{ $f['distance_from_user_km'] }} km</b>
                                @else
                                    {{ $f['city'] }}
                                @endif
                            </span>

                            @if($f['cold_chain_ready'])
                                <span class="text-cyan-700 font-bold text-[10.5px]">❄️ Cold Chain</span>
                            @endif

                            <span class="text-[#1D4ED8] font-bold">
                                {{ $f['active_batches_count'] }} Batch
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-xs">
                        Tidak ada faskes yang cocok dengan filter.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
let map;
let markers = {};

document.addEventListener('DOMContentLoaded', function () {
    const facilities = {!! json_encode($facilities) !!};
    const transfers = {!! json_encode($transfers) !!};

    const defaultLat = {{ $userFacility ? $userFacility->latitude : -6.1754 }};
    const defaultLng = {{ $userFacility ? $userFacility->longitude : 106.8272 }};

    map = L.map('full-map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Color mapper for markers
    function getMarkerColor(type) {
        if (type === 'RSUD') return '#1D4ED8';
        if (type === 'PUSKESMAS') return '#0D9488';
        if (type === 'KLINIK') return '#10B981';
        return '#64748B';
    }

    // Render Markers for each facility
    facilities.forEach(f => {
        const color = getMarkerColor(f.type);
        const isUserFacility = {{ $userFacility ? $userFacility->id : -1 }} === f.id;

        const customIcon = L.divIcon({
            className: 'custom-map-pin',
            html: `
                <div style="
                    background: ${color}; 
                    width: ${isUserFacility ? '34px' : '26px'}; 
                    height: ${isUserFacility ? '34px' : '26px'}; 
                    border-radius: 50%; 
                    border: 3px solid #ffffff; 
                    box-shadow: 0 4px 10px rgba(0,0,0,0.35); 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    color: #fff; 
                    font-size: ${isUserFacility ? '13px' : '10px'}; 
                    font-weight: 800;">
                    ${f.type === 'RSUD' ? 'RS' : (f.type === 'PUSKESMAS' ? 'PK' : 'KL')}
                </div>
            `,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const marker = L.marker([f.latitude, f.longitude], { icon: customIcon }).addTo(map);
        markers[f.name] = marker;

        const popupContent = `
            <div style="font-family: inherit; font-size: 12px; line-height: 1.4; min-width: 200px;">
                <div style="font-weight: 800; font-size: 13px; color: #0B1F4B; margin-bottom: 2px;">${f.name}</div>
                <div style="font-size: 10.5px; color: #64748B; margin-bottom: 8px;">${f.address}</div>
                
                <div style="display: flex; gap: 4px; margin-bottom: 8px; flex-wrap: wrap;">
                    <span style="background: #EFF6FF; color: #1D4ED8; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px;">${f.type}</span>
                    ${f.cold_chain_ready ? '<span style="background: #ECFEFF; color: #0891B2; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px;">❄️ Cold Chain Ready</span>' : ''}
                </div>

                <div style="background: #F8FAFC; padding: 6px 8px; border-radius: 6px; border: 1px solid #E2E8F0; margin-bottom: 8px; font-size: 11px;">
                    <div>Stok Aktif: <b>${f.total_stock} unit</b> (${f.active_batches_count} batch)</div>
                    ${f.at_risk_count > 0 ? `<div style="color: #DC2626; font-weight: 700;">⚠️ ${f.at_risk_count} Batch Berisiko Expired</div>` : '<div style="color: #10B981;">✓ Stok Terkendali</div>'}
                </div>

                <a href="/batches?facility_id=${f.id}" style="display: block; text-align: center; background: #1D4ED8; color: #fff; padding: 5px 8px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 11px;">
                    Lihat Inventaris Faskes &rarr;
                </a>
            </div>
        `;

        marker.bindPopup(popupContent);
    });

    // Render Routes for transfers
    transfers.forEach(t => {
        const isTransit = t.status === 'IN_TRANSIT';
        const isCompleted = t.status === 'COMPLETED';

        const strokeColor = isCompleted ? '#10B981' : (isTransit ? '#1D4ED8' : '#F59E0B');
        const dashArray = isCompleted ? null : '6, 8';

        const polyline = L.polyline([
            [t.donor_lat, t.donor_lng],
            [t.recipient_lat, t.recipient_lng]
        ], {
            color: strokeColor,
            weight: 3,
            opacity: 0.85,
            dashArray: dashArray
        }).addTo(map);

        const routePopup = `
            <div style="font-size: 11.5px; font-family: inherit;">
                <b style="color: #1D4ED8;">Rute: ${t.transfer_code}</b>
                <div style="margin: 3px 0;"><b>${t.donor_name}</b> &rarr; <b>${t.recipient_name}</b></div>
                <div>Obat: <b>${t.medicine_name}</b> (${t.quantity} ${t.unit})</div>
                <div>Jarak: <b>${t.distance_km} km</b> | Status: <b>${t.status}</b></div>
            </div>
        `;
        polyline.bindPopup(routePopup);
    });
});

window.focusFacility = function (lat, lng, name) {
    if (map) {
        map.flyTo([lat, lng], 15, { duration: 1.2 });
        if (markers[name]) {
            setTimeout(() => {
                markers[name].openPopup();
            }, 1200);
        }
    }
};
</script>
@endsection
