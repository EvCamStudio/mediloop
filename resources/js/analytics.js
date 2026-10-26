import Chart from 'chart.js/auto';

/**
 * Inisialisasi semua chart dengan data dari element yang tersedia.
 * Mendeteksi data dari #analytics-data (blade lama) atau #analytics-data-livewire (Livewire component).
 * Fungsi ini dapat dipanggil kembali saat Livewire memperbarui DOM.
 */
function reinitializeCharts() {
    // Coba dapatkan data dari Livewire component terlebih dahulu
    let analytics = {};

    const livewireDataEl = document.getElementById('analytics-data-livewire');
    if (livewireDataEl) {
        try {
            analytics = JSON.parse(livewireDataEl.textContent ?? '{}');
        } catch (e) {
            console.error('Analytics Livewire data parse error', e);
        }
    }

    // Jika tidak ada Livewire data, coba ambil dari blade lama
    if (Object.keys(analytics).length === 0) {
        const dataElement = document.getElementById('analytics-data');
        if (dataElement) {
            try {
                analytics = JSON.parse(dataElement.textContent ?? '{}');
            } catch (e) {
                console.error('Analytics data parse error', e);
            }
        }
    }

    if (Object.keys(analytics).length === 0) return;

    const rootStyles = getComputedStyle(document.documentElement);
    const cssValue = (name) => rootStyles.getPropertyValue(name).trim();
    const documentLocale = document.documentElement.lang || 'en';
    const numberLocale = documentLocale === 'id' ? 'id-ID' : 'en-US';

    const numberFormatter = new Intl.NumberFormat(numberLocale, { maximumFractionDigits: 0 });
    const compactCurrencyFormatter = new Intl.NumberFormat(numberLocale, {
        style: 'currency',
        currency: 'IDR',
        currencyDisplay: 'narrowSymbol',
        notation: 'compact',
        maximumFractionDigits: 1,
    });
    const currencyFormatter = new Intl.NumberFormat(numberLocale, {
        style: 'currency',
        currency: 'IDR',
        currencyDisplay: 'narrowSymbol',
        maximumFractionDigits: 0,
    });

    const palette = {
        primary: cssValue('--color-brand-primary'),
        critical: cssValue('--color-risk-critical'),
        high: cssValue('--color-risk-high'),
        medium: cssValue('--color-risk-medium'),
        low: cssValue('--color-risk-low'),
        grid: cssValue('--color-chart-grid'),
        text: cssValue('--color-text-muted'),
    };

    Chart.defaults.font.family = cssValue('--font-sans') || 'sans-serif';
    Chart.defaults.color = palette.text;
    Chart.defaults.animation = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? { duration: 0 } : true;

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (context) => ` ${context.dataset.label}: ${context.formattedValue}`,
                },
            },
        },
    };

    /**
     * Render pie/donut chart (risk)
     */
    const renderRiskChart = () => {
        const canvas = document.getElementById('riskDonutChart');
        if (!(canvas instanceof HTMLCanvasElement)) return;

        // Destroy existing chart if present
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: analytics.risk.labels,
                datasets: [{
                    data: analytics.risk.values,
                    backgroundColor: [palette.critical, palette.high, palette.medium, palette.low],
                    borderColor: cssValue('--color-surface-raised'),
                    borderWidth: 2,
                    hoverOffset: 4,
                }],
            },
            options: {
                ...baseOptions,
                cutout: '68%',
                plugins: {
                    ...baseOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const total = analytics.risk.values.reduce((sum, value) => sum + Number(value), 0);
                                const percentage = total > 0 ? ((Number(context.raw) / total) * 100).toFixed(1) : '0.0';

                                return ` ${context.label}: ${numberFormatter.format(context.raw)} ${analytics.labels.batches} (${percentage}%)`;
                            },
                        },
                    },
                },
            },
        });

            // Store references for potential destroy on reinit
    window.riskChart = Chart.getChart(canvas);
    };

    /**
     * Render bar chart kategori waste
     */
    const renderCategoryChart = () => {
        const canvas = document.getElementById('categoryWasteChart');
        if (!(canvas instanceof HTMLCanvasElement)) return;

        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: analytics.category.labels,
                datasets: [{
                    label: analytics.labels.waste_value,
                    data: analytics.category.values,
                    backgroundColor: palette.primary,
                    hoverBackgroundColor: cssValue('--color-brand-primary-strong'),
                    borderRadius: 6,
                    maxBarThickness: 38,
                }],
            },
            options: {
                ...baseOptions,
                plugins: {
                    ...baseOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: (context) => ` ${analytics.labels.waste_value}: ${currencyFormatter.format(context.raw)}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10.5 }, maxRotation: 20 },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: palette.grid },
                        ticks: {
                            font: { size: 10 },
                            callback: (value) => compactCurrencyFormatter.format(value),
                        },
                    },
                },
            },
        });

        window.categoryWasteChart = { instance: Chart.getChart(canvas) };
    };

    /**
     * Render bar chart expiry horizon
     */
    const renderHorizonChart = () => {
        const canvas = document.getElementById('expiryHorizonChart');
        if (!(canvas instanceof HTMLCanvasElement)) return;

        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: analytics.horizon.labels,
                datasets: [{
                    label: analytics.labels.batches,
                    data: analytics.horizon.values,
                    backgroundColor: [palette.critical, palette.high, palette.medium, palette.low],
                    borderRadius: 6,
                    maxBarThickness: 32,
                }],
            },
            options: {
                ...baseOptions,
                plugins: {
                    ...baseOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: (context) => ` ${analytics.labels.batches}: ${numberFormatter.format(context.raw)}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: palette.grid },
                        ticks: { precision: 0, font: { size: 10 } },
                    },
                },
            },
        });

        window.expiryHorizonChart = { instance: Chart.getChart(canvas) };
    };

    /**
     * Render bar chart utama (waste per kategori)
     */
    const renderBarChart = () => {
        const canvas = document.getElementById('barChart');
        if (!(canvas instanceof HTMLCanvasElement)) return;

        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: analytics.category.labels,
                datasets: [{
                    label: analytics.labels.waste_value,
                    data: analytics.category.values,
                    backgroundColor: palette.critical,
                    borderRadius: 6,
                    maxBarThickness: 36,
                }],
            },
            options: {
                ...baseOptions,
                plugins: {
                    ...baseOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: (context) => ` ${analytics.labels.waste_value}: ${currencyFormatter.format(context.raw)}`,
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10.5 }, maxRotation: 20 } },
                    y: { beginAtZero: true, grid: { color: palette.grid }, ticks: { font: { size: 10 }, callback: (value) => compactCurrencyFormatter.format(value) } },
                },
            },
        });

        window.barChart = { instance: Chart.getChart(canvas) };
    };

    // Render semua chart
    renderRiskChart();
    renderCategoryChart();
    renderHorizonChart();
    renderBarChart();
}

// Re-initialize charts ketika Livewire memperbarui DOM
// Livewire v3 men-trigger event 'livewire:navigated' saat navigasi dan 'livewire:update' saat data berubah
document.addEventListener('livewire:navigated', reinitializeCharts);
document.addEventListener('livewire:update', reinitializeCharts);

// Jalankan inisialisasi pertama saat halaman dimuat
reinitializeCharts();