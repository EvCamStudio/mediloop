<?php

return [

    'validation' => [
        'required' => 'Kolom ini wajib diisi.',
        'email' => 'Format email belum benar, contoh: nama@faskes.id.',
        'number' => 'Masukkan angka yang valid.',
        'date' => 'Masukkan tanggal yang valid.',
        'min' => 'Nilai terlalu kecil.',
        'max' => 'Nilai terlalu besar.',
        'step' => 'Nilai tidak sesuai kelipatan yang diizinkan.',
        'minlength' => 'Isian terlalu pendek.',
        'maxlength' => 'Isian terlalu panjang.',
        'pattern' => 'Format isian belum sesuai.',
        'invalid' => 'Isian belum valid.',
        'min_value' => 'Nilai minimal :min.',
        'max_value' => 'Nilai maksimal :max.',
        'min_characters' => 'Minimal :min karakter.',
        'max_characters' => 'Maksimal :max karakter.',
        'summary_title' => 'Periksa kembali isian berikut:',
    ],

    'password' => [
        'show' => 'Tampilkan password',
        'hide' => 'Sembunyikan password',
        'show_confirmation' => 'Tampilkan ulangi password',
        'hide_confirmation' => 'Sembunyikan ulangi password',
    ],

    'batch_form' => [
        'days_to_expiry' => '<small>hari</small>',
        'days_to_deplete' => '<small>hari</small>',
        'never_depletes' => 'Tidak habis <small>0/hari</small>',
        'unit_ratio' => '<small>unit (:ratio%)</small>',
        'risk_critical' => 'Kritis',
        'risk_critical_note' => 'Sisa masa edar 30 hari atau kurang; prioritaskan redistribusi.',
        'risk_high' => 'Tinggi',
        'risk_high_note' => 'Risiko tinggi; evaluasi kebutuhan redistribusi.',
        'risk_medium' => 'Sedang',
        'risk_medium_note' => 'Pantau laju pakai dan sisa masa edar.',
        'risk_low' => 'Rendah',
        'risk_low_note' => 'Stok diproyeksikan habis sebelum kedaluwarsa.',
    ],

    'network_map' => [
        'distance_unavailable' => 'Jarak tidak tersedia',
        'distance_from_facility' => ':distance km dari faskes Anda',
        'origin' => 'Faskes asal perhitungan jarak',
        'cold_chain_ready' => 'Siap rantai dingin',
        'zoom_hint' => 'Ctrl + scroll untuk zoom',
        'show_radius' => 'Lihat radius :radius km',
        'invalid_data' => 'Data peta tidak dapat dibaca. Gunakan daftar faskes di bawah.',
    ],

];
