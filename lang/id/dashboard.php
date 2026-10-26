<?php

return [

    'page_title' => 'Ringkasan Logistik',
    'eyebrow' => 'Pusat kendali fasilitas',
    'title' => 'Ringkasan logistik',
    'subtitle' => 'Pantau risiko kedaluwarsa dan peluang redistribusi antar fasilitas.',
    'facility_fallback' => 'Fasilitas kesehatan',
    'manage_stock' => 'Kelola stok',
    'input_batch' => 'Input batch',

    'facility_missing' => [
        'title' => 'Akun belum terhubung ke fasilitas',
        'body' => 'Data stok antar fasilitas tidak ditampilkan sampai akun Anda dihubungkan ke fasilitas terverifikasi. Hubungi administrator platform untuk menyelesaikan verifikasi.',
    ],

    'kpi' => [
        'at_risk' => 'Batch berisiko',
        'batch_unit' => 'batch',
        'high_risk' => ':count risiko tinggi',
        'monitored' => ':count dipantau',
        'active_stock' => 'Stok aktif',
        'batches_recorded' => ':count batch tercatat|:count batch tercatat',
        'risk_value' => 'Nilai berisiko',
        'risk_value_meta' => 'Proyeksi nilai yang dapat diselamatkan',
        'network' => 'Jaringan fasilitas',
        'facility_unit' => 'fasilitas',
        'network_meta' => 'Terhubung untuk redistribusi',
    ],

    'forecast' => [
        'title' => 'Prediksi risiko kedaluwarsa',
        'subtitle' => 'Hari hingga kedaluwarsa dibandingkan laju pemakaian harian fasilitas.',
        'view_all' => 'Lihat semua stok',
        'table_caption' => 'Daftar batch obat aktif beserta stok, stok habis, risiko kedaluwarsa, dan aksi redistribusi.',
        'cold_chain' => 'Perlu rantai dingin',
        'batch_expiry' => 'Batch :batch · Exp :date',
        'expires_in' => 'Kedaluwarsa :days hari',
        'stock_never' => 'tidak habis',
        'stock_out_in' => 'stok habis :days hari',
        'find_recipient' => 'Cari penerima',
        'empty_title' => 'Belum ada data stok',
        'empty_copy' => 'Data obat yang Anda input akan langsung dianalisis untuk mendeteksi risiko limbah kedaluwarsa.',
        'empty_cta' => 'Input stok obat',
        'columns' => [
            'medicine_batch' => 'Obat & batch',
            'physical_stock' => 'Stok fisik',
            'shelf_life' => 'Sisa umur batch',
        ],
    ],

    'match' => [
        'title' => 'Rekomendasi match redistribusi',
        'subtitle_lead' => 'Kandidat untuk',
        'subtitle_tail' => '(batch :batch). Skor: kebutuhan 50% · jarak 30% · logistik 20%.',
        'score_title' => 'Skor = kebutuhan 50% + jarak 30% + logistik 20%',
        'capacity' => 'Kapasitas :count unit',
        'cta' => 'Ajukan transfer',
        'empty_candidates' => 'Belum ada kandidat faskes penerima yang memenuhi syarat rantai dingin dan jarak.',
        'empty_no_risk' => 'Belum ada batch berisiko kedaluwarsa. Tambahkan stok batch untuk mendapatkan rekomendasi transfer otomatis.',
        'note' => 'Rekomendasi bersifat saran. Setiap transfer tetap memerlukan persetujuan petugas faskes.',
    ],

];
