<?php

return [

    'page_title' => 'Peta Jaringan Faskes',
    'title' => 'Peta Jaringan & Jarak Faskes',
    'subtitle' => 'Persebaran fasilitas kesehatan mitra dan jarak udara dari faskes Anda, diurutkan dari yang terdekat.',
    'not_linked' => 'Faskes belum terhubung',
    'manage_stock' => 'Kelola semua stok',

    'stats' => [
        'in_range' => 'Faskes terjangkau',
        'in_range_sub' => 'Dalam radius :radius km',
        'nearest' => 'Faskes terdekat',
        'nearest_sub' => 'Jarak udara (Haversine)',
        'registered' => 'Faskes terdaftar',
        'registered_sub' => 'Setelah filter aktif',
    ],

    'filters' => [
        'aria' => 'Filter jaringan fasilitas',
        'type_label' => 'Tipe faskes',
        'all_types' => 'Semua tipe',
        'cold_chain' => 'Hanya faskes siap rantai dingin',
        'apply' => 'Terapkan filter',
    ],

    'map' => [
        'title' => 'Peta Persebaran Faskes',
        'subtitle_origin' => 'Titik faskes Anda menjadi pusat perhitungan jarak. Lingkaran putus-putus menandai radius :radius km.',
        'subtitle_no_origin' => 'Akun ini belum terhubung ke faskes, sehingga jarak dan radius tidak dihitung.',
        'aria' => 'Peta fasilitas kesehatan. Daftar faskes terdekat tersedia di panel sebelah.',
        'loading' => 'Memuat peta interaktif…',
        'noscript' => 'Peta interaktif memerlukan JavaScript. Seluruh informasi jarak dan faskes tetap tersedia pada daftar di bawah.',
        'source' => 'Jarak dihitung dengan rumus Haversine (jarak udara), bukan jarak tempuh jalan. Koordinat fasilitas merupakan data simulasi untuk demonstrasi.',
    ],

    'legend' => [
        'origin' => 'Faskes Anda',
        'radius' => 'Radius :radius km',
    ],

    'list' => [
        'title' => 'Terdekat dari Faskes Anda',
        'found' => ':count faskes ditemukan. Pilih salah satu untuk menyorot lokasinya di peta.|:count faskes ditemukan. Pilih salah satu untuk menyorot lokasinya di peta.',
        'cold_chain' => 'Siap rantai dingin',
        'proximity' => 'Skor jarak :score%',
        'distance_missing' => 'Jarak tidak tersedia',
        'empty' => 'Belum ada faskes lain yang cocok dengan filter aktif.',
        'show_all' => 'Tampilkan semua faskes',
    ],

];
