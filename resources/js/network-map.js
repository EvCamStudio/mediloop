import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const TILE_ATTRIBUTION =
    '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors';

function hasCoordinates(point) {
    return Number.isFinite(point?.lat) && Number.isFinite(point?.lng);
}

function distanceLabel(distanceKm) {
    if (!Number.isFinite(distanceKm)) {
        return 'Jarak tidak tersedia';
    }

    return `${distanceKm.toFixed(2).replace('.', ',')} km dari faskes Anda`;
}

function originIcon() {
    return L.divIcon({
        className: 'ml-map-marker-shell',
        html: '<span class="ml-map-marker ml-map-marker-origin"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M12 4v16M4 12h16"/></svg></span>',
        iconSize: [30, 30],
        iconAnchor: [15, 15],
        popupAnchor: [0, -14],
    });
}

function facilityIcon(rank) {
    return L.divIcon({
        className: 'ml-map-marker-shell',
        html: `<span class="ml-map-marker ml-map-marker-facility">${rank}</span>`,
        iconSize: [28, 28],
        iconAnchor: [14, 14],
        popupAnchor: [0, -12],
    });
}

function buildPopup(facility, isOrigin = false) {
    const root = document.createElement('div');
    root.className = 'ml-map-popup';

    const title = document.createElement('p');
    title.className = 'ml-map-popup-title';
    title.textContent = facility.name;

    const meta = document.createElement('p');
    meta.className = 'ml-map-popup-meta';
    meta.textContent = `${facility.type} · ${facility.address}`;

    const distance = document.createElement('p');
    distance.className = 'ml-map-popup-distance';
    distance.textContent = isOrigin ? 'Faskes asal perhitungan jarak' : distanceLabel(facility.distanceKm);

    root.append(title, meta, distance);

    if (facility.coldChainReady) {
        const coldChain = document.createElement('p');
        coldChain.className = 'ml-map-popup-cold';
        coldChain.textContent = 'Siap rantai dingin';
        root.append(coldChain);
    }

    return root;
}

function addZoomHint(map) {
    const hint = L.control({ position: 'bottomleft' });

    hint.onAdd = () => {
        const element = L.DomUtil.create('div', 'ml-map-hint');
        element.textContent = 'Ctrl + scroll untuk zoom';

        return element;
    };

    hint.addTo(map);
}

function addRadiusControl(map, radiusBounds, maxRadiusKm) {
    const control = L.control({ position: 'topright' });

    control.onAdd = () => {
        const button = L.DomUtil.create('button', 'ml-map-radius-btn');
        button.type = 'button';
        button.textContent = `Lihat radius ${maxRadiusKm} km`;
        L.DomEvent.disableClickPropagation(button);
        button.addEventListener('click', () => {
            map.fitBounds(radiusBounds, { padding: [24, 24] });
        });

        return button;
    };

    control.addTo(map);
}

function initNetworkMap() {
    const container = document.getElementById('network-map');
    const dataElement = document.getElementById('network-map-data');

    if (!container || !dataElement) {
        return;
    }

    let data;

    try {
        data = JSON.parse(dataElement.textContent ?? '{}');
    } catch {
        container.querySelector('.ml-network-map-fallback')?.replaceChildren(
            document.createTextNode('Data peta tidak dapat dibaca. Gunakan daftar faskes di bawah.')
        );

        return;
    }

    const origin = hasCoordinates(data.origin) ? data.origin : null;
    const facilities = (Array.isArray(data.facilities) ? data.facilities : []).filter(hasCoordinates);
    const maxRadiusKm = Number(data.maxRadiusKm) || 50;

    const fallback = container.querySelector('.ml-network-map-fallback');
    fallback?.remove();

    const map = L.map(container, {
        scrollWheelZoom: false,
        zoomControl: true,
        attributionControl: true,
    });

    L.tileLayer(TILE_URL, {
        maxZoom: 19,
        attribution: TILE_ATTRIBUTION,
    }).addTo(map);

    addZoomHint(map);

    const markers = new Map();
    const bounds = L.latLngBounds([]);

    if (origin) {
        L.marker([origin.lat, origin.lng], {
            icon: originIcon(),
            keyboard: true,
            title: `${origin.name} (faskes Anda)`,
            alt: `${origin.name}, faskes Anda`,
            zIndexOffset: 1000,
        })
            .bindPopup(buildPopup(origin, true))
            .addTo(map);

        const radiusCircle = L.circle([origin.lat, origin.lng], {
            radius: maxRadiusKm * 1000,
            color: '#1D4ED8',
            weight: 1.5,
            dashArray: '6 6',
            fillColor: '#1D4ED8',
            fillOpacity: 0.04,
            interactive: false,
        }).addTo(map);

        addRadiusControl(map, radiusCircle.getBounds(), maxRadiusKm);

        bounds.extend([origin.lat, origin.lng]);
    }

    facilities.forEach((facility, index) => {
        const marker = L.marker([facility.lat, facility.lng], {
            icon: facilityIcon(index + 1),
            keyboard: true,
            title: facility.name,
            alt: `${facility.name}, ${distanceLabel(facility.distanceKm)}`,
        })
            .bindPopup(buildPopup(facility))
            .addTo(map);

        markers.set(String(facility.id), marker);
        bounds.extend([facility.lat, facility.lng]);

        marker.on('click', () => {
            highlightTarget(facility.id);
        });
    });

    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [36, 36], maxZoom: 14 });
    } else {
        map.setView([-6.1754, 106.8272], 12);
    }

    bindTargets(map, markers);

    let resizeFrame = null;

    window.addEventListener('resize', () => {
        if (resizeFrame !== null) {
            cancelAnimationFrame(resizeFrame);
        }

        resizeFrame = requestAnimationFrame(() => {
            map.invalidateSize();
            resizeFrame = null;
        });
    });
}

function highlightTarget(facilityId) {
    document.querySelectorAll('.ml-network-target.is-active').forEach((element) => {
        element.classList.remove('is-active');
    });

    const target = document.querySelector(`.ml-network-target[data-facility-id="${facilityId}"]`);
    target?.classList.add('is-active');
}

function bindTargets(map, markers) {
    document.querySelectorAll('.ml-network-target[data-facility-id]').forEach((target) => {
        target.addEventListener('click', () => {
            const marker = markers.get(target.dataset.facilityId);

            if (!marker) {
                return;
            }

            highlightTarget(target.dataset.facilityId);
            map.flyTo(marker.getLatLng(), Math.max(map.getZoom(), 14), { duration: 0.6 });
            marker.openPopup();
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNetworkMap);
} else {
    initNetworkMap();
}
