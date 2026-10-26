<?php

return [

    'page_title' => 'Analitik & Statistik Prediktif',
    'eyebrow' => 'Analitik pendukung keputusan',
    'title' => 'Analitik & Visualisasi Prediktif',
    'subtitle' => 'Dihitung dari laju pemakaian, estimasi limbah kedaluwarsa, dan efisiensi redistribusi.',
    'filter_label' => 'Fasilitas',
    'all_facilities' => 'Semua fasilitas (wilayah)',
    'apply_filter' => 'Terapkan filter',
    'currency' => 'Rupiah (IDR)',

    'kpi' => [
        'inventory_label' => 'Total nilai inventaris',
        'inventory_units' => ':count unit fisik|:count unit fisik',
        'inventory_batches' => ':count batch|:count batch',
        'waste_label' => 'Potensi kerugian limbah',
        'waste_units' => ':count unit terancam|:count unit terancam',
        'waste_ratio' => ':ratio dari stok',
        'efficiency_label' => 'Skor efisiensi inventaris',
        'efficiency_note' => 'Berdasarkan laju pemakaian obat',
        'saved_label' => 'Penyelamatan redistribusi',
        'saved_units' => ':count unit diselamatkan|:count unit diselamatkan',
        'saved_status' => 'Transfer selesai',
    ],

    'units' => [
        'unit' => 'unit',
        'units' => 'unit',
        'batch' => 'batch',
        'batches' => 'batch',
        'per_day' => 'unit/hari',
    ],

    'risk' => [
        'title' => 'Distribusi tingkat risiko obat',
        'subtitle' => 'Proporsi batch berdasarkan sisa hari kedaluwarsa dan laju konsumsi',
        'batch_count' => ':count batch|:count batch',
        'critical' => 'Kritis',
        'high' => 'Tinggi',
        'medium' => 'Sedang',
        'low' => 'Aman',
        'critical_with_code' => 'Kritis (Critical)',
        'high_with_code' => 'Tinggi (High)',
        'medium_with_code' => 'Sedang (Medium)',
        'low_with_code' => 'Aman (Low)',
    ],

    'category' => [
        'title' => 'Potensi nilai limbah per golongan obat (Rp)',
        'subtitle' => 'Estimasi kerugian finansial jika surplus tidak diredistribusi ke fasilitas lain',
    ],

    'horizon' => [
        'title' => 'Horizon waktu kedaluwarsa',
        'subtitle' => 'Jumlah batch yang mendekati tanggal kedaluwarsa',
        'under_30' => 'Darurat (≤ 30 hari)',
        'days_31_60' => 'Waspada (31–60 hari)',
        'days_61_90' => 'Perhatian (61–90 hari)',
        'over_90' => 'Aman (> 90 hari)',
    ],

    'bar_chart' => [
        'title' => 'Nilai Limbah per Kategori Obat',
        'subtitle' => 'Estimasi nilai limbah per nama obat',
        'aria' => 'Diagram batang nilai limbah per kategori obat',
    ],

    'surplus' => [
        'title' => 'Indeks surplus tertinggi (perlu segera dialihkan)',
        'subtitle' => 'Obat dengan waktu habis jauh melebihi sisa hari kedaluwarsa (rasio surplus > 1)',
        'manage_stock' => 'Kelola stok →',
        'empty' => 'Belum ada obat dengan surplus signifikan.',
        'medicine_batch' => 'Nama obat & batch',
        'stock_burn' => 'Stok / laju harian',
        'deplete_vs_expiry' => 'Hari habis vs kedaluwarsa',
        'surplus_ratio' => 'Rasio surplus',
        'potential_waste' => 'Potensi limbah',
        'days_to_deplete' => ':days hari habis',
        'days_to_expiry' => 'sisa :days hari',
        'ratio' => ':ratio× surplus',
    ],

    'charts' => [
        'waste_value' => 'Estimasi limbah (Rp)',
        'batches' => 'Batch',
        'batch' => 'batch',
        'risk_aria' => 'Diagram donat distribusi risiko batch obat',
        'category_aria' => 'Diagram batang potensi nilai limbah per golongan obat',
        'horizon_aria' => 'Diagram batang jumlah batch menurut horizon kedaluwarsa',
    ],

    'empty' => [
        'title' => 'Belum ada data batch aktif',
        'description' => 'Analitik akan tampil setelah fasilitas Anda mencatat batch obat aktif.',
        'cta' => 'Input batch',
    ],

];
