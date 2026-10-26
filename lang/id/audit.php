<?php

return [

    'page_title' => 'Pengawasan & Audit Regional',
    'breadcrumb' => 'Pengawasan & audit',
    'breadcrumb_aria' => 'Breadcrumb',
    'eyebrow' => 'Pengawasan regional',
    'title' => 'Pengawasan Logistik & Audit Redistribusi',
    'subtitle' => 'Telusuri transaksi redistribusi yang selesai dan jejak perubahan status inventaris lintas fasilitas. Halaman ini tidak memuat data pasien.',
    'default_facility' => 'Dinas Kesehatan',

    'kpi' => [
        'units_saved' => 'Unit terselamatkan',
        'units_saved_meta' => 'Dari :count transfer selesai',
        'value_saved' => 'Nilai terselamatkan',
        'value_saved_meta' => 'Berdasarkan harga satuan batch',
        'facilities_connected' => 'Faskes terhubung',
        'facilities_connected_meta' => 'Di luar fasilitas Dinas Kesehatan',
        'facility_unit' => 'faskes',
        'active_batches' => 'Batch aktif terpantau',
        'active_batches_meta' => 'Batch berstatus aktif saat ini',
        'batch_unit' => 'batch',
    ],

    'completed' => [
        'title' => 'Rekap Transaksi Redistribusi Berhasil',
        'subtitle' => 'Transfer selesai dengan mutasi stok yang sudah tercatat. Diurutkan dari waktu selesai terbaru.',
        'count' => ':count transfer selesai|:count transfer selesai',
        'empty_title' => 'Belum ada transfer selesai',
        'empty_copy' => 'Rekap akan muncul setelah transfer dikonfirmasi selesai dan stok dimutasi.',
        'table_aria' => 'Tabel rekap transfer selesai, dapat digeser horizontal untuk melihat semua kolom',
        'table_caption' => 'Rekap transfer redistribusi yang telah selesai, termasuk obat, rute fasilitas, jumlah, nilai, kurir, dan waktu penyelesaian.',
        'default_courier' => 'Kurir khusus farmasi',
        'tracking' => 'Resi :number',
        'columns' => [
            'code' => 'Kode transfer',
            'medicine_batch' => 'Obat & batch',
            'donor_recipient' => 'Donor → penerima',
            'value' => 'Nilai (IDR)',
            'courier' => 'Kurir & resi',
            'completed_at' => 'Waktu selesai (WIB)',
        ],
    ],

    'logs' => [
        'title' => 'Jejak Audit Sistem',
        'subtitle' => 'Catatan tindakan perubahan status dan mutasi inventaris, diurutkan dari yang terbaru.',
        'count' => ':count entri|:count entri',
        'empty_title' => 'Belum ada rekaman log audit',
        'empty_copy' => 'Setiap perubahan status atau mutasi inventaris akan tercatat di sini.',
        'table_aria' => 'Tabel jejak audit sistem, dapat digeser horizontal untuk melihat semua kolom',
        'table_caption' => 'Jejak audit sistem berisi waktu, aktor, fasilitas terkait, kode aksi, dan deskripsi perubahan.',
        'system_actor' => 'Sistem Otomatis',
        'staff_default' => 'Petugas',
        'system_activity' => 'Aktivitas sistem',
        'no_description' => 'Tidak ada deskripsi.',
        'columns' => [
            'time' => 'Waktu (WIB)',
            'actor' => 'Aktor pengguna',
            'facility' => 'Faskes terkait',
            'action' => 'Aksi',
            'description' => 'Deskripsi perubahan',
        ],
    ],

];
