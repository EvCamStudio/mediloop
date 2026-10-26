# Dokumentasi Perubahan & Peningkatan Fitur (Branch: `fix_zoe`)

Dokumen ini mencatat seluruh perubahan, perbaikan bug, penyempurnaan UI/UX, dan refaktor sistem yang telah dikerjakan sejak bercabang dari `origin/main`. Dokumen ini disusun secara rinci agar rekan tim dan asisten AI lainnya dapat memahami konteks teknis, logika algoritma, berkas yang diubah, serta cara kerja setiap fitur yang telah disempurnakan.

---

## 1. Ringkasan Eksekutif & Struktur Git

- **Branch Asal**: `origin/main`
- **Branch Kerja**: `fix_zoe`
- **Tujuan Branch**: Mengintegrasikan perbaikan komprehensif pada sistem redistribusi obat, transparansi skor rekomendasi berbasis kebutuhan klinis riil, penyempurnaan visual grafik donat SVG, perbaikan navigasi peta Leaflet, serta pembersihan redundansi layout pada halaman stok dan dashboard.
- **Dampak ke Branch Tim**: Branch `fix_zoe` dibuat terpisah dan independen dari branch `main`. Kode tim pada `origin/main` tidak akan tertimpa atau terganggu. Penggabungan ke `main` dapat dilakukan secara aman melalui mekanisme Pull Request (PR) setelah tim selesai meninjau perubahan ini.

---

## 2. Rincian Modul & Fitur yang Diperbaiki

### A. Sistem Rekomendasi Redistribusi Obat & Penilaian Kebutuhan Klinis Riil
- **Masalah Sebelumnya**:
  1. Halaman Dashboard hanya mengunci rekomendasi pada satu batch obat kritis teratas (Insulin Glargine). Akibatnya, seluruh kartu rekomendasi selalu bertuliskan "Insulin Glargine" (batch `INS-2026-X8`) dan pengguna tidak bisa melihat faskes yang membutuhkan obat lain (seperti Ceftriaxone, Paracetamol, Amoxicillin, atau Azithromycin).
  2. Seluruh rumah sakit kandidat memiliki persentase kecocokan yang hampir seragam (~94%) dan kapasitas serap yang statis (50 unit). Hal ini terjadi karena faskes mitra lain belum memiliki data inventaris aktif sehingga sistem mengasumsikan stok kosong dan kebutuhan statis.
  3. Pengguna tidak mengetahui dari mana asal angka persentase tersebut dan mengapa faskes tertentu menjadi prioritas.
- **Perubahan & Solusi**:
  1. **Multi-Medicine Selector (Tab & Baris Tabel Interaktif)**:
     - Dibuat tab pill interaktif di atas kartu rekomendasi yang menampilkan seluruh obat berisiko di fasilitas pengguna.
     - Setiap baris obat di tabel ringkasan atas (`.ml-table-dashboard`) kini dapat diklik langsung (atau melalui tombol *Rekomendasi* di kolom aksi).
     - Mengklik obat langsung memicu fungsi JavaScript `selectRecBatch(batchId)` yang secara instan mengganti panel rekomendasi obat di bawahnya, menyorot baris tabel aktif dengan warna biru (`.is-selected-batch`), dan menyelaraskan parameter URL `?batch_id=...` tanpa reload halaman.
  2. **Algoritma Skor Kebutuhan Dinamis (`RedistributionEngine.php`)**:
     - Skor kecocokan ($100\%$) dihitung secara transparan dari 3 bobot:
       $$\text{Skor} = (0.50 \times \text{Kebutuhan Riil}) + (0.30 \times \text{Jarak}) + (0.20 \times \text{Logistik})$$
     - **Kebutuhan Riil (50%)** dihitung dari sisa stok faskes penerima terhadap laju pemakaian hariannya ($\text{Sisa Stok} / \text{Laju Konsumsi}$):
       - $\le 3$ hari: **Defisit Kritis** (Skor 98%)
       - $4 - 7$ hari: **Stok Menipis** (Skor 90%)
       - $8 - 14$ hari: **Kebutuhan Moderat** (Skor 75%)
       - $15 - 30$ hari: **Stok Cukup** (Skor 45%)
       - $> 30$ hari: **Surplus/Melimpah** (Skor 15%, otomatis turun dari rekomendasi utama)
     - **Jarak Terdekat (30%)** dihitung dengan rumus Haversine berdasarkan koordinat GPS faskes mitra (radius maksimal 50 km).
     - **Kesiapan Logistik (20%)** mewajibkan kesiapan rantai dingin (*cold chain*) untuk obat dingin seperti Insulin (skor 95%). Faskes tanpa fasilitas pendingin (seperti klinik pratama biasa) otomatis digugurkan dari kandidat penerima obat dingin.
  3. **Kotak Transparansi Skor pada Setiap Kartu**:
     - Setiap kartu kandidat faskes kini memuat kotak rincian penilaian prioritas (*Skor Kebutuhan*, *Skor Jarak*, dan *Kesiapan Logistik*) beserta status riil sisa stok faskes tersebut (contoh: *"Defisit Kritis: Sisa 12 unit (3 hari pemakaian)"*).
     - Kapasitas serap dihitung secara matematis berdasarkan laju serap faskes sebelum tanggal kedaluwarsa batch tiba.
  4. **Seeding Data Inventaris Mitra yang Realistis (`MediLoopSeeder.php`)**:
     - Mengisi data stok dan laju konsumsi spesifik untuk seluruh faskes mitra di Jakarta Pusat (RSUD Tanah Abang, RS PGI Cikini, Puskesmas Sawah Besar, Puskesmas Tanah Abang, dan Klinik Harmoni).
     - Hasilnya, setiap obat merekomendasikan rumah sakit yang benar-benar mengalami defisit:
       - **Insulin**: Prioritas #1 RSUD Tanah Abang (sisa 3 hari).
       - **Ceftriaxone**: Prioritas #1 RS PGI Cikini (sisa 2 hari untuk tindakan bedah).
       - **Amoxicillin**: Prioritas #1 Puskesmas Sawah Besar (sisa 2 hari).
       - **Paracetamol Infus**: Prioritas #1 RSUD Tanah Abang (sisa 2 hari).
       - **Azithromycin**: Prioritas #1 Puskesmas Tanah Abang (sisa 2 hari).
- **Berkas yang Diubah**:
  - `app/Http/Controllers/DashboardController.php`
  - `app/Services/RedistributionEngine.php`
  - `database/seeders/MediLoopSeeder.php`
  - `resources/views/dashboard.blade.php`
  - `resources/views/layouts/mediloop.blade.php`

---

### B. Zoom Otomatis Peta Interaktif Leaflet
- **Masalah Sebelumnya**:
  Peta interaktif jaringan faskes (`/network`) dan pemilih lokasi (`/facility-requests`) dikonfigurasi dengan `scrollWheelZoom: false`. Roda scroll mouse diabaikan sepenuhnya dan pengguna terpaksa mengklik tombol zoom `+`/`-` manual. Terdapat pula label informasi pojok bawah "Ctrl + scroll untuk zoom" yang tidak berfungsi.
- **Perubahan & Solusi**:
  1. Mengaktifkan `scrollWheelZoom: true` pada `resources/js/network-map.js` dan `resources/js/facility-map-picker.js`.
  2. Menghapus teks instruksi usang "Ctrl + scroll untuk zoom" agar antarmuka peta bersih dan natural.
  3. Melakukan kompilasi ulang aset produksi dengan Vite (`npm run build`).
  4. Pengujian browser menunjukkan scroll mouse ke depan dan ke belakang langsung memperbesar/memperkecil peta secara mulus tanpa memerlukan tombol keyboard.
- **Berkas yang Diubah**:
  - `resources/js/network-map.js`
  - `resources/js/facility-map-picker.js`
  - `public/build/*` (Aset terkompilasi Vite)

---

### C. Grafik Donat Komposisi Risiko Stok & Tipografi Dashboard
- **Masalah Sebelumnya**:
  Tampilan statistik risiko stok obat pada dashboard sempat menggunakan kotak metrik yang tidak proporsional dan terdapat duplikasi baris data "Total Nilai Berisiko" di bagian bawah yang mengulang isi KPI atas. Lingkaran donat juga terlalu kecil dibandingkan teks di sekitarnya.
- **Perubahan & Solusi**:
  1. **Pembesaran Grafik Donat**:
     - Dimensi grafik donat SVG diperbesar menjadi `200px` (diameter) dengan radius `r="80"` dan ketebalan garis `stroke-width="18"`.
     - Tipografi nilai persentase di tengah donat diperbesar menjadi `34px` dengan warna dinamis yang otomatis mengikuti tingkat keparahan risiko tertinggi (Merah untuk Kritis, Oranye untuk Tinggi, Kuning untuk Sedang, Hijau untuk Aman).
  2. **Legenda Ringkas & Minimalis**:
     - Mengubah keterangan status ("Kritis", "Tinggi", "Sedang", "Aman") di bawah grafik menjadi baris ramping berukuran font 11px dan indikator dot 7px. Baris dengan jumlah 0 batch otomatis diredupkan (opacity 0.42).
  3. **Pembersihan Duplikasi Data**:
     - Menghapus baris "Total Nilai Berisiko" di footer kartu statistik donat karena metrik tersebut sudah tercantum pada kartu ringkasan KPI di bagian atas.
  4. **Pencegahan Kolom Tabel Terpotong**:
     - Menyesuaikan lebar grid kanan (`325px`) dan padding sel tabel dashboard (`11px 8px`) pada `resources/views/layouts/mediloop.blade.php` sehingga kolom "Aksi" pada tabel sebelah kiri tidak pernah terpotong pada resolusi desktop standar (1280px).
- **Berkas yang Diubah**:
  - `resources/views/dashboard.blade.php`
  - `resources/views/layouts/mediloop.blade.php`

---

### D. Penataan Halaman Stok & Inventaris Batch (`/batches`)
- **Masalah Sebelumnya**:
  Halaman stok obat dipenuhi oleh 4 kartu KPI berukuran besar dan banner rekomendasi transfer utama di bagian atas, sehingga pengguna harus melakukan scroll jauh ke bawah untuk melihat tabel stok obat yang sebenarnya.
- **Perubahan & Solusi**:
  1. Menghapus 4 kartu KPI dan banner match utama dari halaman `resources/views/batches/index.blade.php`.
  2. Halaman langsung menampilkan bilah filter status, pencarian, dan tabel data inventaris obat di posisi teratas.
  3. Menstandarkan perataan teks pada kolom "Stok Tersedia" dan "Proyeksi Terbuang" ke sisi kiri dengan tata letak angka dan satuan yang rapi.
- **Berkas yang Diubah**:
  - `resources/views/batches/index.blade.php`

---

### E. Halaman Pengusulan Transfer Interaktif (`/transfers/propose`)
- **Masalah Sebelumnya**:
  Pada halaman pencarian faskes penerima transfer, tombol radio atau tombol "Pilih" terpisah terkesan kaku dan nomor urut peringkat faskes tidak bereaksi saat faskes dipilih.
- **Perubahan & Solusi**:
  1. Seluruh kartu kandidat faskes penerima dijadikan elemen interaktif yang dapat diklik langsung.
  2. Menghapus tombol "Pilih" ganda yang redundan.
  3. Kartu yang terpilih otomatis diberi efek sorotan visual (border biru, background lembut, dan tanda centang aktif) serta nomor peringkat faskes otomatis menyesuaikan faskes yang aktif dipilih.
- **Berkas yang Diubah**:
  - `resources/views/transfers/propose.blade.php`
  - `resources/views/layouts/mediloop.blade.php`

---

### F. Import Batch CSV Cerdas (`BatchController.php`)
- **Peningkatan**:
  - Menambahkan modul rekonsiliasi obat otomatis pada proses import CSV di `app/Http/Controllers/BatchController.php`.
  - Mendukung parsing tanggal fleksibel dalam format Indonesia maupun internasional (`d/m/Y`, `Y-m-d`, dsb.).
  - Menyediakan penanganan kesalahan baris CSV yang informatif dan rute unduh template batch.
- **Berkas yang Diubah**:
  - `app/Http/Controllers/BatchController.php`
  - `routes/web.php`

---

### G. Integrasi Fitur dari `origin/main` (Analitik, Multi-Bahasa, & Solusi Deploy)
- **Konteks & Analisis Masalah**:
  1. Rekan tim mengerjakan fitur analitik statistik dan lokalisasi bahasa (Indonesia / Inggris) langsung pada `origin/main`, namun saat di-deploy tampilan desain tidak berubah atau mengalami kegagalan build.
  2. Penyebabnya adalah kegagalan build Vite akibat pustaka `chart.js` belum terpasang di `node_modules`, adanya duplikasi metode pada `BatchController`, rute yang tidak valid (`AdminFacilityController::destroy`), serta upaya mengganti sistem rekomendasi interaktif dengan Livewire sederhana yang justru merusak fungsionalitas multi-obat.
- **Perubahan & Solusi Integrasi**:
  1. **Penggabungan Fitur Analitik / Statistik (`/analytics`)**:
     - Mengintegrasikan rute `/analytics` dan controller `AnalyticsController` serta layanan `AnalyticsService`.
     - Menginstal ketergantungan `chart.js` (`npm install`) dan menyelaraskan kompilasi Vite pada `resources/js/analytics.js`.
     - Menambahkan tautan navigasi "Statistik & Analitik" pada sidebar dan bottom bar navigasi seluler `layouts/mediloop.blade.php`.
  2. **Penggabungan Sistem Multi-Bahasa (ID / EN)**:
     - Mengintegrasikan berkas bahasa lengkap pada `lang/id/` dan `lang/en/` beserta `lang/id.json`.
     - Mengaktifkan middleware `SetLocale` pada `bootstrap/app.php` dan endpoint `/locale`.
     - Menjalankan migrasi database kolom `locale` pada tabel `users`.
     - Menyematkan komponen pengalih bahasa instan (`<x-language-switcher variant="compact" />`) pada topbar header di samping avatar profil pengguna.
     - Menyinkronkan label-label tampilan tanpa merusak atribut HTML, selector, atau logika JavaScript interaktif.
  3. **Penjagaan Penuh Desain & Solusi Kita**:
     - Desain Donut Chart 200px SVG, posisi keterangan di bawah donat, tab pemilih multi-obat, kartu transparansi skor 3 bobot, dan sorotan baris tabel **100% terjaga utuh**.
     - Halaman stok obat (`/batches`) tetap langsung menyajikan tabel inventaris tanpa kartu KPI dan banner redundan di bagian atas.
     - Zoom otomatis roda mouse pada Leaflet map tetap aktif dan mulus.
- **Berkas yang Diubah / Ditambahkan**:
  - `app/Http/Controllers/AnalyticsController.php`
  - `app/Services/AnalyticsService.php`
  - `app/Http/Controllers/LocaleController.php`
  - `app/Http/Middleware/SetLocale.php`
  - `config/localization.php`
  - `database/migrations/2026_10_25_133513_add_locale_to_users_table.php`
  - `resources/views/analytics/index.blade.php`
  - `resources/js/analytics.js`
  - `resources/views/components/language-switcher.blade.php`
  - `lang/id/*` dan `lang/en/*`

---

## 3. Daftar Berkas yang Dimodifikasi

Berikut adalah daftar berkas utama yang diselaraskan dan disempurnakan di branch `fix_zoe`:

1. `app/Http/Controllers/BatchController.php` - Fitur import CSV, penanganan duplikasi fungsi, dan lokalisasi pesan audit/flash.
2. `app/Http/Controllers/DashboardController.php` - Penyiapan data rekomendasi multi-obat dan batch selector.
3. `app/Http/Controllers/AnalyticsController.php` - Controller modul analitik statistik dari `main`.
4. `app/Http/Controllers/LocaleController.php` - Pengontrol pergantian bahasa pengguna (ID/EN).
5. `app/Http/Controllers/TransferController.php` - Alur usulan transfer antar-faskes.
6. `app/Services/RedistributionEngine.php` - Logika penilaian kecocokan riil (50% kebutuhan, 30% jarak, 20% logistik).
7. `app/Services/AnalyticsService.php` - Kalkulasi metrik KPI, distribusi risiko, horizon kedaluwarsa, dan anomali surplus.
8. `database/seeders/MediLoopSeeder.php` - Seeding inventaris dan kebutuhan klinis realistis faskes mitra Jakarta.
9. `resources/views/dashboard.blade.php` - Donut chart besar, legenda ramping, tab selector rekomendasi obat, kartu transparansi skor.
10. `resources/views/layouts/mediloop.blade.php` - Penataan CSS tabel, tab selector, kartu prioritas, menu navigasi analitik, dan language switcher di topbar.
11. `resources/views/batches/index.blade.php` - Pembersihan banner atas dan penataan langsung tabel stok tanpa KPI redundan.
12. `resources/views/transfers/index.blade.php` - Tampilan daftar dan riwayat transfer dengan tombol modal usulan baru.
13. `resources/views/transfers/propose.blade.php` - Kartu faskes penerima interaktif dan responsif tanpa tombol ganda.
14. `resources/views/analytics/index.blade.php` - Halaman analitik dan visualisasi Chart.js.
15. `resources/views/components/language-switcher.blade.php` - Komponen pemilih bahasa Indonesia dan Inggris.
16. `resources/js/network-map.js` - Pengaktifan scroll wheel zoom otomatis Leaflet dan pembersihan hint usang.
17. `resources/js/facility-map-picker.js` - Pengaktifan scroll wheel zoom pada modul pemilih koordinat faskes.
18. `resources/js/analytics.js` - Skrip render Chart.js untuk visualisasi data analitik.
19. `routes/web.php` - Rute template import CSV, analitik, dan perbaikan penanganan rute pencabutan undangan faskes.
20. `public/build/*` - Aset terkompilasi frontend (Vite manifest, CSS, JS, Chart.js bundler).

---

## 4. Status Pengujian & Kualitas Kode

Seluruh berkas dan modul telah diverifikasi secara ketat melalui suite pengujian otomatis:
- **Laravel Pint (Code Style & PSR-12)**:
  `./vendor/bin/pint --test` -> **PASSED (0 errors)**
- **PHPStan Static Analysis (Level Maksimal Konfigurasi)**:
  `./vendor/bin/phpstan analyse --memory-limit=1G` -> **PASSED (0 errors)**
- **Asset Compilation (Vite & Rolldown)**:
  `npm run build` -> **BUILT SUCCESSFULLY in 3.76s**
- **Headless Chrome Browser Testing**:
  Pengujian rendering visual dan interaktivitas JavaScript (tab switching, scroll zoom peta, pemilihan faskes transfer) terverifikasi berjalan lancar.

---

## 5. Panduan Sinkronisasi Deployment

Agar perubahan ini langsung terlihat pada server deployment:
1. Kode lengkap dan teruji berada pada branch `fix_zoe`.
2. Jika server CI/CD atau Vercel/VPS melakukan deployment dari branch `main`, lakukan penggabungan (merge) branch `fix_zoe` ke `main` lalu push ke `origin/main`:
   ```bash
   git checkout main
   git merge fix_zoe --ff-only
   git push origin main
   ```
3. Pastikan pada server deployment dijalankan:
   - `npm install` (agar dependensi `chart.js` terpasang)
   - `npm run build` (agar file asset produksi terbarui)
   - `php artisan migrate` (agar kolom `locale` ditambahkan ke database)
