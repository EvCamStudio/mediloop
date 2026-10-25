const getElement = (id) => document.getElementById(id);

const isBatchFormPresent = () => getElement('batchForm') !== null;

const numberFormatter = new Intl.NumberFormat('id-ID');
const decimalFormatter = new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 1,
    maximumFractionDigits: 1,
});

const syncMedicineMode = () => {
    const toggle = getElement('toggleNewMedicine');
    const existingSection = getElement('existingMedicineSection');
    const newSection = getElement('newMedicineSection');
    const medicineSelect = getElement('medicine_id');

    if (! toggle || ! existingSection || ! newSection || ! medicineSelect) {
        return;
    }

    const isNewMedicine = toggle.checked;

    existingSection.hidden = isNewMedicine;
    newSection.hidden = ! isNewMedicine;

    if (isNewMedicine) {
        medicineSelect.removeAttribute('required');
    } else {
        medicineSelect.setAttribute('required', 'required');
    }
};

const localDateFromInput = (value) => {
    const [year, month, day] = value.split('-').map(Number);

    return new Date(year, month - 1, day);
};

const formatLocalDate = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const applyDefaultExpiryDate = () => {
    const input = getElement('expiry_date');

    if (! input || input.value) {
        return;
    }

    const defaultDate = new Date();
    defaultDate.setDate(defaultDate.getDate() + 60);
    input.value = formatLocalDate(defaultDate);
};

const autofillUnitPrice = () => {
    const select = getElement('medicine_id');
    const input = getElement('unit_price_idr');

    if (! select || ! input) {
        return;
    }

    const selected = select.options[select.selectedIndex];

    if (selected?.dataset.price && ! input.value) {
        input.value = selected.dataset.price;
    }
};

const buildRiskLevel = (projectedWaste, daysToExpiry, wasteRatio) => {
    if (projectedWaste > 0 && daysToExpiry <= 30) {
        return {
            label: 'Kritis',
            className: 'ml-risk ml-risk-critical',
            note: 'Sisa masa edar ≤30 hari; prioritaskan redistribusi.',
        };
    }

    if (projectedWaste > 0 && (daysToExpiry <= 60 || wasteRatio >= 0.25)) {
        return {
            label: 'Tinggi',
            className: 'ml-risk ml-risk-high',
            note: 'Risiko tinggi; evaluasi kebutuhan redistribusi.',
        };
    }

    if (projectedWaste > 0) {
        return {
            label: 'Sedang',
            className: 'ml-risk ml-risk-medium',
            note: 'Pantau laju pakai dan sisa masa edar.',
        };
    }

    return {
        label: 'Rendah',
        className: 'ml-risk ml-risk-low',
        note: 'Stok diproyeksikan habis sebelum kedaluwarsa.',
    };
};

const previewElements = () => {
    const elements = {
        stockInput: getElement('current_stock'),
        burnRateInput: getElement('daily_burn_rate'),
        expiryInput: getElement('expiry_date'),
        daysExpiry: getElement('previewDaysExpiry'),
        daysDeplete: getElement('previewDaysDeplete'),
        waste: getElement('previewWaste'),
        risk: getElement('previewRisk'),
        riskNote: getElement('previewRiskNote'),
    };

    return Object.values(elements).every(Boolean) ? elements : null;
};

const updatePreview = () => {
    const elements = previewElements();

    if (! elements || ! elements.expiryInput.value) {
        return;
    }

    const stock = parseInt(elements.stockInput.value, 10) || 0;
    const burnRate = parseFloat(elements.burnRateInput.value) || 0;
    const expiryDate = localDateFromInput(elements.expiryInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const daysToExpiry = Math.max(0, Math.ceil((expiryDate.getTime() - today.getTime()) / 86400000));
    const expectedConsumption = Math.floor(burnRate * daysToExpiry);
    const projectedWaste = Math.max(0, stock - expectedConsumption);
    const wasteRatio = stock > 0 ? projectedWaste / stock : 0;

    elements.daysExpiry.innerHTML = `${numberFormatter.format(daysToExpiry)} <small>hari</small>`;

    if (burnRate > 0) {
        elements.daysDeplete.innerHTML = `${decimalFormatter.format(stock / burnRate)} <small>hari</small>`;
    } else {
        elements.daysDeplete.innerHTML = 'Tidak habis <small>0/hari</small>';
    }

    elements.waste.innerHTML = `${numberFormatter.format(projectedWaste)} <small>unit (${Math.round(wasteRatio * 100)}%)</small>`;

    const level = buildRiskLevel(projectedWaste, daysToExpiry, wasteRatio);

    elements.risk.textContent = level.label;
    elements.risk.className = level.className;
    elements.riskNote.textContent = level.note;
};

const handleChange = (event) => {
    if (! (event.target instanceof Element) || ! isBatchFormPresent()) {
        return;
    }

    if (event.target.id === 'toggleNewMedicine') {
        syncMedicineMode();

        return;
    }

    if (event.target.id === 'medicine_id') {
        autofillUnitPrice();

        return;
    }

    if (event.target.id === 'expiry_date') {
        updatePreview();
    }
};

const handleInput = (event) => {
    if (! (event.target instanceof Element) || ! isBatchFormPresent()) {
        return;
    }

    if (event.target.id === 'current_stock' || event.target.id === 'daily_burn_rate') {
        updatePreview();
    }
};

const initializeBatchForm = () => {
    if (! isBatchFormPresent()) {
        return;
    }

    syncMedicineMode();
    applyDefaultExpiryDate();
    updatePreview();
};

let isInitialized = false;

const initialize = () => {
    if (isInitialized) {
        return;
    }

    isInitialized = true;

    document.addEventListener('change', handleChange);
    document.addEventListener('input', handleInput);
    document.addEventListener('livewire:navigated', initializeBatchForm);

    initializeBatchForm();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize);
} else {
    initialize();
}
