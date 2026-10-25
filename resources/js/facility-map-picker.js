import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const TILE_ATTRIBUTION =
    '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>';

const container = document.getElementById('facility-map');

if (container) {
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const coordinateLabel = document.getElementById('map-coordinates');

    const initialLat = Number.parseFloat(container.dataset.lat);
    const initialLng = Number.parseFloat(container.dataset.lng);
    const readonly = container.dataset.readonly === 'true';
    const start = Number.isFinite(initialLat) && Number.isFinite(initialLng)
        ? [initialLat, initialLng]
        : [-6.1754, 106.8272];

    if (readonly) {
        container.classList.add('is-readonly');
    }

    const map = L.map(container, {
        center: start,
        zoom: 14,
        scrollWheelZoom: true,
    });

    L.tileLayer(TILE_URL, {
        attribution: TILE_ATTRIBUTION,
        maxZoom: 19,
    }).addTo(map);

    const marker = L.marker(start, {
        draggable: !readonly,
        icon: L.divIcon({
            className: 'ml-map-marker-shell',
            html: '<span class="ml-map-marker ml-map-marker-origin"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M12 4v16M4 12h16"/></svg></span>',
            iconSize: [30, 30],
            iconAnchor: [15, 15],
        }),
    }).addTo(map);

    const format = (value) => value.toFixed(7);

    function updateCoordinates(lat, lng) {
        if (latitudeInput) {
            latitudeInput.value = format(lat);
        }

        if (longitudeInput) {
            longitudeInput.value = format(lng);
        }

        if (coordinateLabel) {
            coordinateLabel.textContent = `${format(lat)}, ${format(lng)}`;
        }
    }

    function moveMarker(latlng) {
        marker.setLatLng(latlng);
        updateCoordinates(latlng.lat, latlng.lng);
    }

    map.on('click', (event) => {
        if (!readonly) {
            moveMarker(event.latlng);
        }
    });

    marker.on('dragend', () => {
        const latlng = marker.getLatLng();
        updateCoordinates(latlng.lat, latlng.lng);
    });

    updateCoordinates(start[0], start[1]);
}
