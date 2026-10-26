<?php

return [

    'accept' => [
        'page_title' => 'Terima Undangan',
        'eyebrow' => 'Undangan tim',
        'heading' => 'Bergabung dengan :facility',
        'invited_as' => 'Anda diundang sebagai',
        'invited_description' => 'Aktifkan akun untuk mulai mengelola stok dan koordinasi redistribusi fasilitas.',
        'facility_fallback' => 'Fasilitas kesehatan',
        'invited_by' => 'Diundang oleh',
        'expires_at' => 'Berlaku sampai',
        'single_use' => 'Undangan ini hanya dapat digunakan satu kali. Jika tautan kedaluwarsa, minta undangan baru dari kepala fasilitas.',
        'activate_title' => 'Aktifkan akun',
        'activate_description' => 'Isi nama lengkap dan buat password untuk menyelesaikan pendaftaran.',
        'full_name' => 'Nama lengkap',
        'name_required' => 'Nama lengkap wajib diisi.',
        'password_required' => 'Kata sandi wajib diisi.',
        'password_minlength' => 'Kata sandi minimal 8 karakter.',
        'password_help' => 'Gunakan kata sandi yang kuat dan tidak dipakai di layanan lain.',
        'password_confirmation' => 'Ulangi kata sandi',
        'password_confirmation_required' => 'Konfirmasi kata sandi wajib diisi.',
        'activate_button' => 'Aktifkan akun',
        'footnote' => 'Setelah akun aktif, Anda dapat masuk menggunakan email :email.',
        'welcome' => 'Selamat datang di MediLoop, :name.',
    ],

    'invalid' => [
        'page_title' => 'Undangan Tidak Valid',
        'heading' => 'Undangan tidak dapat digunakan',
        'back_to_login' => 'Ke halaman masuk',
    ],

    'errors' => [
        'email_taken' => 'Email ini sudah memiliki akun MediLoop.',
        'facility_inactive' => 'Fasilitas harus aktif sebelum mengundang anggota tim.',
        'not_found' => 'Undangan tidak ditemukan.',
        'already_used' => 'Undangan ini sudah digunakan.',
        'expired' => 'Undangan ini sudah kedaluwarsa. Minta undangan baru dari kepala fasilitas.',
        'facility_inactive_token' => 'Fasilitas ini sedang tidak aktif. Hubungi administrator platform.',
    ],

    'validation' => [
        'name_required' => 'Nama lengkap wajib diisi.',
        'password_confirmation_mismatch' => 'Konfirmasi kata sandi tidak sama.',
    ],

    'audit' => [
        'invited' => 'Undangan dikirim ke :email untuk :facility.',
        'joined' => ':name bergabung dengan :facility.',
        'revoked' => 'Undangan untuk :email dibatalkan.',
    ],

];
