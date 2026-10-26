# MediLoop — Platform Kolaborasi & Redistribusi Obat Antar-Fasilitas Kesehatan

MediLoop adalah sistem pendukung keputusan (*Clinical Decision Support System - DSS*) dan platform operasional redistribusi obat cerdas antar-fasilitas kesehatan (Rumah Sakit, Puskesmas, dan Klinik). Platform ini dirancang untuk mencegah pemborosan obat kedaluwarsa di faskes yang mengalami surplus, sekaligus menanggulangi krisis defisit obat darurat di faskes lain melalui algoritma pencocokan kebutuhan riil, kalkulasi jarak geospasial terdekat, dan jaminan kesiapan rantai dingin (*cold chain*).

---

## 1. Narasi & Latar Belakang Masalah

### Problem Statement
Setiap tahunnya, fasilitas pelayanan kesehatan di Indonesia menghadapi ironi ganda dalam tata kelola logistik farmasi:
1. **Pemborosan Obat Berlebih (*Medication Waste*)**: Rumah sakit rujukan sering kali menimbun stok obat dalam jumlah besar yang berakhir kedaluwarsa dan dimusnahkan karena laju konsumsi tidak sebanding dengan sisa masa simpan batch obat.
2. **Kekosongan Obat Kritis (*Stockout*)**: Di saat yang bersamaan, puskesmas kecamatan atau klinik di wilayah sekitar mengalami kekosongan obat-obatan esensial (seperti insulin, antibiotik injeksi, dan cairan infus), yang secara langsung mengancam keselamatan pasien rawat inap dan gawat darurat.
3. **Hambatan Birokrasi & Rantai Dingin**: Proses redistribusi obat antar-instansi selama ini terkendala oleh koordinasi manual yang lambat, ketiadaan visibilitas stok lintas faskes, serta kekhawatiran rusaknya mutu obat termolabil (rantai dingin 2°C - 8°C) selama proses transportasi.

### Solusi MediLoop
MediLoop hadir sebagai jembatan ekosistem farmasi yang transparan, aman, dan akuntabel. Melalui algoritma pencocokan terotomatisasi, sistem ini mengubah potensi limbah obat bernilai miliaran rupiah menjadi pasokan penyelamat nyawa bagi faskes mitra yang membutuhkan, lengkap dengan kepatuhan audit regulasi dan validasi fisik bertingkat.

---

## 2. Fitur Unggulan Sistem

### A. Algoritma Pencocokan Kebutuhan Riil (Skor 3 Bobot)
Sistem pendukung keputusan MediLoop tidak sekadar mencocokkan nama obat, melainkan menghitung skor kecocokan prioritas ($100\%$) secara transparan berdasarkan formula:

$$\text{Skor Prioritas} = (0.50 \times \text{Kebutuhan Riil}) + (0.30 \times \text{Jarak Terdekat}) + (0.20 \times \text{Kesiapan Logistik})$$

1. **Kebutuhan Riil (Bobot 50%)**: 
   Dihitung dari sisa stok faskes penerima terhadap laju konsumsi hariannya ($\text{Sisa Stok} / \text{Laju Konsumsi}$):
   - $\le 3$ hari: **Defisit Kritis** (Skor 98%) — Prioritas Tertinggi.
   - $4 - 7$ hari: **Stok Menipis** (Skor 90%).
   - $8 - 14$ hari: **Kebutuhan Moderat** (Skor 75%).
   - $15 - 30$ hari: **Stok Cukup** (Skor 45%).
   - $> 30$ hari: **Surplus/Melimpah** (Skor 15%, otomatis turun dari rekomendasi).
2. **Jarak Geospasial Terdekat (Bobot 30%)**:
   Menggunakan rumus Haversine berdasarkan koordinat GPS presisi faskes dalam radius maksimal 50 km untuk meminimalkan waktu tempuh pengiriman.
3. **Kesiapan Rantai Dingin / Logistik (Bobot 20%)**:
   Wajib memiliki fasilitas rantai dingin aktif (2°C - 8°C) untuk obat termolabil seperti Insulin. Faskes tanpa fasilitas pendingin terverifikasi otomatis digugurkan dari kandidat penerima obat dingin demi menjaga integritas klinis obat.

### B. Dashboard Interaktif & Donut Chart Komposisi Risiko
- **Grafik Donat 200px SVG**: Visualisasi dinamis komposisi risiko stok faskes (Kritis, Tinggi, Sedang, Aman) dengan warna tematik adaptif dan legenda ramping.
- **Dropdown Selektor Multi-Obat**: Memungkinkan petugas farmasi beralih antar-batch obat berisiko secara instan dengan sinkronisasi dua arah ke tabel inventaris tanpa reload halaman.
- **Kartu Transparansi Skor**: Setiap faskes yang direkomendasikan menyajikan rincian penilaian klinis, estimasi kapasitas serap obat sebelum kedaluwarsa, dan status defisit stok riil faskes penerima.

### C. Alur Transfer Obat Bertingkat (*State Machine*)
Alur redistribusi obat dirancang sesuai standar Cara Distribusi Obat yang Baik (CDOB):
1. **Usulan Transfer (*Proposed*)**: Faskes donor memilih batch dan mengajukan alokasi unit ke faskes kandidat terbaik.
2. **Persetujuan Pengirim (*Approved*)**: Kepala/Apoteker penanggung jawab faskes donor menyetujui pelepasan stok.
3. **Konfirmasi Penerima (*Accepted*)**: Faskes penerima mengonfirmasi kesiapan menerima stok obat.
4. **Pengiriman (*In Transit*)**: Faskes donor menginput nomor resi/surat jalan fisik dan jenis armada ekspedisi farmasi.
5. **Verifikasi Penerimaan Fisik (*Completed*)**: Faskes penerima memeriksa fisik barang, nomor batch, dan suhu rantai dingin, lalu menyelesaikan transfer ke sistem inventaris mereka.

### D. Peta Jaringan Faskes Interaktif (Leaflet)
Peta geospasial sebaran rumah sakit, puskesmas, dan klinik mitra dengan dukungan *auto scroll wheel zoom* untuk navigasi visual yang natural.

### E. Modul Statistik & Analitik Real-Time
- Visualisasi Chart.js terintegrasi Livewire polling (pembaruan otomatis tiap 60 detik) untuk memantau nilai aset terselamatkan, rasio potensi limbah, dan horizon kedaluwarsa obat.
- Warna indikator risiko yang sinkron 100% dengan dashboard (Merah Kritis `#DC2626`, Oranye Tinggi `#EA580C`, Kuning Sedang `#D97706`, Hijau Aman `#16A34A`).
- Mekanisme format angka mandiri tanpa mewajibkan ekstensi PHP `intl` agar stabil di semua lingkungan server.

### F. Simulator Algoritma Interaktif pada Landing Page
Simulator publik yang memungkinkan pengunjung dan juri menguji perhitungan matematis algoritma pendeteksi risiko obat ($D_{\text{dep}}$, $W$, rasio limbah, dan status risiko) secara interaktif tanpa overlapping elemen antarmuka.

---

## 3. Alur Penggunaan Aplikasi (User Journey)

### Alur 1: Faskes Pengirim (Donor) — Mencegah Obat Kedaluwarsa
1. Masuk (*login*) menggunakan akun faskes pengirim (misal: RSUD Tarakan).
2. Pada **Dashboard**, amati grafik komposisi risiko stok dan tabel batch berisiko.
3. Pilih batch obat yang ingin diredistribusikan melalui dropdown atau klik langsung pada baris tabel overview.
4. Sistem otomatis menampilkan daftar rekomendasi faskes terdekat yang benar-benar membutuhkan obat tersebut beserta rincian skor kecocokannya.
5. Klik **Ajukan Transfer** pada faskes penerima yang dipilih.
6. Masukkan jumlah unit obat yang akan dialokasikan, lalu kirim usulan transfer.
7. Setelah faskes penerima menyetujui, input nomor resi kurir dan kirimkan barang.

### Alur 2: Faskes Penerima (Recipient) — Mengatasi Defisit Pasokan
1. Masuk menggunakan akun faskes penerima (misal: Puskesmas Gambir).
2. Buka menu **Transfers & Allocation** ([`/transfers`](file:///home/arisyah/.gemini/antigravity/scratch/mediloop-main/routes/web.php#L54)).
3. Tinjau usulan transfer masuk dari faskes mitra.
4. Klik **Terima Alokasi** jika jumlah dan spesifikasi obat sesuai kebutuhan.
5. Pantau nomor resi pengiriman pada detail transfer.
6. Saat paket obat tiba, periksa fisik kemasan, nomor batch, serta indikator suhu, lalu klik **Konfirmasi Penerimaan Fisik**. Stok obat otomatis bertambah ke inventaris faskes penerima.

### Alur 3: Administrator Platform — Tata Kelola Ekosistem
1. Masuk menggunakan akun Administrator Platform ([`admin@mediloop.test`](mailto:admin@mediloop.test)).
2. Buka menu **Admin Overview** ([`/admin`](file:///home/arisyah/.gemini/antigravity/scratch/mediloop-main/routes/web.php#L80)) untuk melihat metrik agregat seluruh faskes se-wilayah.
3. Buka **Registri Fasilitas** ([`/admin/facilities`](file:///home/arisyah/.gemini/antigravity/scratch/mediloop-main/routes/web.php#L82)) untuk memverifikasi faskes baru, mengubah profil faskes, serta mengelola staf dan undangan anggota faskes.
4. Tinjau permohonan faskes baru pada menu **Permohonan Faskes** ([`/admin/facility-requests`](file:///home/arisyah/.gemini/antigravity/scratch/mediloop-main/routes/web.php#L89)).

---

## 4. Akun Demonstrasi Pengujian (Demo Accounts)

Semua akun demo di bawah ini menggunakan kata sandi standar: `mediloop#demo2026`

| Peran Akun | Alamat Email | Fasilitas Terhubung | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Administrator Platform** | `admin@mediloop.test` | Kantor Dinkes DKI | Registri faskes, verifikasi onboarding, manajemen user platform |
| **Faskes Pengirim (Donor)** | `donor@mediloop.test` | RSUD Tarakan | Manajemen stok batch obat, monitoring risiko, pengajuan transfer donor |
| **Kepala Faskes Donor** | `donor.head@mediloop.test` | RSUD Tarakan | Otorisasi persetujuan pengiriman obat donor |
| **Faskes Penerima (Recipient)** | `recipient@mediloop.test` | Puskesmas Gambir | Monitoring defisit stok, penerimaan alokasi, konfirmasi fisik barang |
| **Kepala Faskes Penerima** | `recipient.head@mediloop.test` | Puskesmas Gambir | Otorisasi penerimaan obat dari faskes mitra |
| **Pengawas Farmasi (Auditor)** | `auditor@mediloop.test` | Dinkes DKI Jakarta | Pengawasan audit trail regional, analisis limbah lintas faskes |

---

## 5. Panduan Instalasi Lokal

### Prasyarat Sistem
- PHP >= 8.2 (dengan ekstensi `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- Database MySQL atau MariaDB

### Langkah Instalasi
```bash
# 1. Clone repositori
git clone https://github.com/EvCamStudio/mediloop.git
cd mediloop

# 2. Pasang dependensi backend
composer install

# 3. Pasang dependensi frontend (Chart.js, Leaflet, dsb.)
npm install

# 4. Konfigurasi Environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database pada .env, kemudian jalankan migrasi & seeding data realistis
php artisan migrate:fresh --seed

# 6. Kompilasi aset frontend
npm run build

# 7. Jalankan server lokal
php artisan serve
```

Aplikasi dapat langsung diakses melalui browser pada alamat: `http://127.0.0.1:8000`

---

## 6. Panduan Deployment Produksi

Jika melakukan deployment pada platform hosting (Vercel, VPS, Railway, dsb.):

```bash
git checkout main
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Tumpukan Teknologi (Tech Stack)

- **Backend Framework**: Laravel 12 (PHP 8.3)
- **Komponen Reaktif**: Livewire 3
- **Database**: MariaDB / MySQL dengan Eloquent ORM
- **Visualisasi & Peta**:
  - Chart.js 4.6 (Grafik tren analitik)
  - Leaflet JS 1.9 (Peta sebaran faskes geospasial)
  - SVG Native (Grafik donat presisi tinggi)
- **Styling**: Tailwind CSS & MediLoop Design System Tokens
- **Autentikasi & Keamanan**: Laravel Fortify, Passkeys / WebAuthn, Two-Factor Authentication (2FA)
- **Kualitas Kode**:
  - Laravel Pint (Standar PSR-12)
  - PHPStan (Analisis statis level ketat, 0 errors)

---

## 8. Lisensi & Hak Cipta

Dikembangkan oleh Tim MediLoop untuk kompetisi inovasi digital sektor kesehatan. Hak cipta dilindungi undang-undang.
