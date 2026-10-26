<?php

return [

    'breadcrumb' => 'Redistribusi',
    'breadcrumb_aria' => 'Breadcrumb',
    'batch_label' => 'Batch :batch',
    'default_storage_location' => 'Gudang Farmasi Penerima',

    'units' => [
        'per_day' => 'unit/hari',
    ],

    'risk' => [
        'high' => 'Risiko tinggi',
    ],

    'tabs' => [
        'awaiting_donor' => 'Menunggu donor',
        'awaiting_recipient' => 'Menunggu penerima',
        'ready_to_ship' => 'Siap kirim',
        'in_transit' => 'Dalam pengiriman',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],

    'table' => [
        'transfer' => 'Transfer',
        'medicine_quantity' => 'Obat & jumlah',
        'facility_route' => 'Rute fasilitas',
        'score' => 'Skor',
    ],

    'compliance' => [
        'title' => 'Kepatuhan regulasi kefarmasian',
        'proposal' => 'MediLoop adalah sistem pendukung keputusan, bukan marketplace. Redistribusi tetap memerlukan persetujuan Kepala Instalasi Farmasi donor dan verifikasi penerimaan fisik oleh Kepala Fasilitas penerima.',
        'tracking' => 'MediLoop adalah sistem pendukung keputusan, bukan marketplace. Setiap otorisasi transfer tetap memerlukan persetujuan petugas berwenang dan verifikasi penerimaan fisik.',
    ],

    'index' => [
        'page_title' => 'Daftar Redistribusi Obat',
        'eyebrow' => 'Koordinasi antarfasilitas',
        'title' => 'Redistribusi & hibah obat',
        'subtitle' => 'Lacak status pengajuan dari otorisasi donor hingga mutasi stok fisik.',
        'propose_from_stock' => 'Usulkan dari stok',
        'tabs_aria' => 'Filter status transfer',
        'list_title' => 'Daftar transfer obat',
        'table_caption' => 'Daftar transfer dan status alurnya',
        'empty_title' => 'Belum ada transfer pada kategori ini',
        'empty_copy' => 'Usulan transfer muncul setelah stok berisiko diajukan dari dashboard.',
        'empty_cta' => 'Kembali ke dashboard',
        'air_distance' => 'jarak udara',
        'track' => 'Lacak',
    ],

    'propose' => [
        'page_title' => 'Rekomendasi & Pengusulan Transfer',
        'breadcrumb' => 'Usulan batch :batch',
        'eyebrow' => 'Rekomendasi redistribusi',
        'title' => 'Cari penerima untuk :medicine',
        'subtitle' => 'Kandidat diperingkatkan dari kebutuhan, jarak, dan kesiapan logistik.',

        'source' => [
            'title' => 'Batch sumber',
            'meta' => ':facility · batch :batch',
            'help' => 'Proyeksi limbah dihitung dari selisih stok dan laju pemakaian sampai tanggal kedaluwarsa.',
        ],

        'metrics' => [
            'donor_stock' => 'Stok donor',
            'burn_rate' => 'Laju pakai',
            'remaining_expiry' => 'Sisa kedaluwarsa',
            'projected_waste' => 'Proyeksi terbuang',
        ],

        'candidates' => [
            'title' => 'Kandidat penerima',
            'weights' => '50% kebutuhan · 30% jarak udara · 20% logistik',
            'count' => ':count faskes|:count faskes',
            'sort_aria' => 'Urutkan kandidat',
            'sort_best' => 'Skor terbaik',
            'sort_nearest' => 'Terdekat',
            'sort_note' => 'Jarak udara (Haversine), bukan jarak tempuh jalan.',
            'empty_title' => 'Belum ada kandidat penerima',
            'empty_copy' => 'Tidak ada fasilitas yang memenuhi syarat rantai dingin dan jarak saat ini.',
            'need' => 'Kebutuhan',
            'distance' => 'Jarak',
            'logistics' => 'Logistik',
            'capacity' => 'Kapasitas',
            'select' => 'Pilih',
        ],

        'form' => [
            'title' => 'Ajukan transfer',
            'subtitle' => 'Usulan diteruskan ke Kepala Instalasi Farmasi donor.',
            'recipient_facility' => 'Fasilitas penerima',
            'quantity' => 'Jumlah unit',
            'quantity_required' => 'Jumlah unit wajib diisi.',
            'quantity_min' => 'Jumlah minimal 1 unit.',
            'quantity_max' => 'Jumlah melebihi stok tersedia (:max unit).',
            'quantity_help' => 'Maksimal :max :unit tersedia di donor.',
            'notes' => 'Catatan justifikasi',
            'notes_maxlength' => 'Catatan maksimal 500 karakter.',
            'notes_placeholder' => 'Jelaskan kebutuhan penerima dan alasan redistribusi.',
            'notes_default' => 'Pengusulan redistribusi surplus stok :medicine untuk mencegah pemusnahan obat kedaluwarsa dan mendukung ketersediaan di fasilitas sekitar.',
            'proposer' => 'Pengusul:',
            'submit' => 'Ajukan transfer',
        ],
    ],

    'show' => [
        'page_title' => 'Lacak Transfer #:code',
        'eyebrow' => 'Transfer obat · :code',
        'errors_heading' => 'Periksa kembali isian berikut:',
        'awaiting' => 'Menunggu',
        'submitted_at' => 'Diajukan :date',
        'score' => 'Skor :score%',
        'back_to_list' => 'Kembali ke daftar',

        'status' => [
            'proposed' => 'Menunggu otorisasi donor',
            'approved' => 'Menunggu kesediaan penerima',
        ],

        'stages' => [
            'proposed' => 'Pengusulan',
            'approved' => 'Otorisasi donor',
            'accepted' => 'Kesediaan penerima',
            'in_transit' => 'Pengiriman',
            'completed' => 'Penerimaan & mutasi',
        ],

        'progress' => [
            'title' => 'Progres transfer',
            'subtitle' => 'Setiap tahap memerlukan otorisasi pihak yang berwenang.',
            'stage_count' => '5 TAHAP',
        ],

        'invariant' => [
            'pending_title' => 'Stok donor belum dipotong',
            'pending_body' => 'Mutasi stok donor dan penerima hanya dijalankan saat penerima mengonfirmasi penerimaan fisik. Sebelum tahap itu, stok donor tetap utuh.',
            'completed_title' => 'Mutasi stok selesai',
            'completed_body' => ':quantity :unit telah dipotong dari :donor dan ditambahkan ke :recipient pada :date.',
        ],

        'action' => [
            'title' => 'Tindakan tahap ini',
            'awaiting_approval' => 'Menunggu persetujuan Kepala Instalasi Farmasi fasilitas donor.',
            'approve' => 'Setujui pengeluaran obat',
            'approve_note' => 'Hanya Kepala Instalasi Farmasi fasilitas donor yang berwenang menyetujui tahap ini.',
            'awaiting_acceptance' => 'Donor telah menyetujui. Menunggu kesediaan menerima dari fasilitas penerima.',
            'accept' => 'Konfirmasi kesediaan menerima',
            'accept_note' => 'Hanya Apoteker atau Kepala Fasilitas penerima yang berwenang.',
            'ready_to_dispatch' => 'Penerima siap. Masukkan rincian kurir untuk memberangkatkan obat.',
            'courier_label' => 'Jenis kurir / transportasi',
            'courier_default' => 'Kurir farmasi berpendingin (cold-box)',
            'tracking_label' => 'Nomor resi / surat jalan',
            'tracking_placeholder' => 'Contoh: EXP-DKI-2026-001',
            'tracking_help' => 'Isi sesuai nomor resi atau surat jalan fisik.',
            'dispatch' => 'Berangkatkan pengiriman',
            'dispatch_note' => 'Hanya staf fasilitas donor yang dapat memberangkatkan pengiriman.',
            'in_transit_label' => 'Sedang dalam perjalanan',
            'tracking_short' => 'Resi',
            'verify_copy' => 'Penerima memeriksa kondisi fisik, segel kemasan, dan suhu dingin sebelum mengonfirmasi penerimaan.',
            'complete' => 'Konfirmasi penerimaan fisik & mutasi stok',
            'complete_note' => 'Menunggu konfirmasi Kepala Fasilitas penerima sebelum stok dimutasi.',
            'cancel_confirm' => 'Batalkan transfer ini? Tindakan tidak dapat dibatalkan.',
            'cancel' => 'Batalkan transfer',
        ],

        'details' => [
            'title' => 'Detail logistik',
            'subtitle' => 'Rute, jumlah, dan hasil pencocokan kandidat.',
            'index_label' => 'DATA',
            'batch' => 'Batch',
            'donor' => 'Donor',
            'recipient' => 'Penerima',
            'distance' => 'Jarak udara',
            'intake_cap' => 'Batas serapan penerima',
            'notes_empty' => 'Tidak ada catatan tambahan.',
        ],

        'audit' => [
            'title' => 'Jejak audit',
            'subtitle' => 'Riwayat otorisasi dan perubahan status.',
            'index_label' => 'LOG',
            'empty_title' => 'Belum ada catatan audit',
            'empty_copy' => 'Aktivitas transfer akan muncul di sini.',
            'system' => 'Sistem',
        ],

        'summary' => [
            'title' => 'Ringkasan transfer',
            'donor' => 'Donor',
            'recipient' => 'Penerima',
            'note' => 'Rekomendasi bersifat saran. Setiap transfer memerlukan persetujuan petugas fasilitas.',
        ],

        'scores' => [
            'need' => 'Kebutuhan (50%)',
            'proximity' => 'Jarak (30%)',
            'logistics' => 'Logistik (20%)',
            'match' => 'Skor kecocokan',
        ],
    ],

    'messages' => [
        'propose_forbidden' => 'Anda hanya dapat mengusulkan redistribusi untuk fasilitas Anda sendiri.',
        'batch_unavailable' => 'Batch ini tidak tersedia untuk redistribusi.',
        'quantity_exceeds_stock' => 'Jumlah transfer melebihi stok yang tersedia.',
        'recipient_invalid' => 'Fasilitas penerima harus aktif dan berbeda dari fasilitas donor.',
        'access_denied' => 'Anda tidak memiliki akses ke transfer ini.',
        'not_proposed_stage' => 'Transfer ini tidak berada pada tahap persetujuan donor.',
        'approve_forbidden' => 'Hanya Kepala Instalasi Farmasi faskes donor yang dapat menyetujui pengeluaran obat.',
        'not_approved_stage' => 'Transfer ini belum disetujui faskes donor.',
        'accept_forbidden' => 'Hanya perwakilan faskes penerima yang dapat menerima transfer ini.',
        'not_accepted_stage' => 'Pengiriman hanya dapat diproses setelah faskes penerima menyetujui transfer.',
        'dispatch_forbidden' => 'Hanya staf faskes donor yang dapat memberangkatkan pengiriman.',
        'not_in_transit_stage' => 'Penerimaan fisik hanya dapat dikonfirmasi saat obat dalam perjalanan.',
        'complete_forbidden' => 'Hanya Kepala/Direktur faskes penerima yang berwenang memvalidasi penerimaan fisik obat.',
        'cancel_not_allowed' => 'Transfer pada tahap ini tidak dapat dibatalkan.',
        'cancel_forbidden' => 'Anda tidak berwenang membatalkan transfer ini.',
        'cancel_reason_default' => 'Dibatalkan oleh pengguna.',

        'proposed_flash' => 'Usulan transfer :code berhasil diajukan dan menunggu persetujuan Kepala Instalasi Farmasi.',
        'approved_flash' => 'Usulan transfer telah disetujui donor. Menunggu konfirmasi kesediaan penerima.',
        'accepted_flash' => 'Transfer diterima oleh faskes tujuan. Faskes donor dapat segera memproses pengiriman.',
        'in_transit_flash' => 'Status pengiriman diperbarui menjadi Dalam Perjalanan (In Transit).',
        'completed_flash' => 'Penerimaan fisik berhasil diverifikasi! Stok obat telah dimutasi secara atomik.',
        'cancelled_flash' => 'Transfer berhasil dibatalkan.',

        'audit_proposed' => 'Pengusulan transfer :code oleh :name untuk :quantity unit ke :recipient.',
        'audit_approved' => 'Persetujuan formal pengeluaran obat disahkan oleh :name (:job_title).',
        'audit_accepted' => 'Faskes penerima (:facility) menyetujui penerimaan alokasi obat.',
        'audit_dispatched' => 'Obat diberangkatkan menggunakan :courier dengan nomor resi/kontrol: :tracking.',
        'audit_completed' => 'Transfer :code berhasil diselesaikan. Diterima :quantity unit :medicine (batch: :batch).',
        'audit_cancelled' => 'Transfer :code dibatalkan. Alasan: :reason',
    ],

];
