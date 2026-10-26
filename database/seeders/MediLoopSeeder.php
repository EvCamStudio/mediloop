<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MediLoopSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Healthcare Facilities (DKI Jakarta Cluster)
        $rsudTarakan = Facility::create([
            'name' => 'RSUD Tarakan Jakarta',
            'code' => 'FSK-RSUD-TRK',
            'type' => 'RSUD',
            'ownership' => 'PEMERINTAH',
            'license_number' => '445/001/RSUD-DKI/2022',
            'address' => 'Jl. Kyai Caringin No. 7, Cideng',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'district' => 'Jakarta Pusat',
            'latitude' => -6.1729000,
            'longitude' => 106.8122000,
            'cold_chain_ready' => true,
            'contact_person' => 'Apt. Hendra Wijaya, S.Farm',
            'phone' => '021-3842934',
            'email' => 'rsud.tarakan@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        $puskesmasGambir = Facility::create([
            'name' => 'Puskesmas Kecamatan Gambir',
            'code' => 'FSK-PKM-GMB',
            'type' => 'PUSKESMAS',
            'license_number' => '440/012/PKM-JP/2023',
            'address' => 'Jl. Tanah Abang II No. 27, Petojo Selatan',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.1764000,
            'longitude' => 106.8285000,
            'cold_chain_ready' => true,
            'contact_person' => 'Apt. Dewi Sartika, S.Farm',
            'phone' => '021-3501234',
        ]);

        $rsudTanahAbang = Facility::create([
            'name' => 'RSUD Tanah Abang',
            'code' => 'FSK-RSUD-TNA',
            'type' => 'RSUD',
            'license_number' => '445/002/RSUD-DKI/2023',
            'address' => 'Jl. KH. Mas Mansyur No. 30, Kebon Kacang',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.1912000,
            'longitude' => 106.8164000,
            'cold_chain_ready' => true,
            'contact_person' => 'Apt. Firman Syahputra, S.Farm',
            'phone' => '021-3154821',
        ]);

        $puskesmasSawahBesar = Facility::create([
            'name' => 'Puskesmas Kecamatan Sawah Besar',
            'code' => 'FSK-PKM-SWB',
            'type' => 'PUSKESMAS',
            'license_number' => '440/015/PKM-JP/2023',
            'address' => 'Jl. Mangga Dua Dalam No. 1, Mangga Dua Selatan',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.1415000,
            'longitude' => 106.8290000,
            'cold_chain_ready' => true,
            'contact_person' => 'Apt. Ratna Sari, S.Farm',
            'phone' => '021-6258901',
        ]);

        $puskesmasTanahAbang = Facility::create([
            'name' => 'Puskesmas Kecamatan Tanah Abang',
            'code' => 'FSK-PKM-TNA',
            'type' => 'PUSKESMAS',
            'license_number' => '440/018/PKM-JP/2023',
            'address' => 'Jl. Danau Toba No. 5, Bendungan Hilir',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.2085000,
            'longitude' => 106.8145000,
            'cold_chain_ready' => true,
            'contact_person' => 'dr. Lukman Hakim',
            'phone' => '021-5743210',
        ]);

        $klinikHarmoni = Facility::create([
            'name' => 'Klinik Pratama Harmoni Medika',
            'code' => 'FSK-KLN-HRM',
            'type' => 'KLINIK',
            'license_number' => '503/044/KLN-DKI/2024',
            'address' => 'Jl. Hayam Wuruk No. 108',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.1648000,
            'longitude' => 106.8201000,
            'cold_chain_ready' => false,
            'contact_person' => 'dr. Rian Pratama',
            'phone' => '021-6387890',
        ]);

        $rsPgiCikini = Facility::create([
            'name' => 'RS PGI Cikini',
            'code' => 'FSK-RS-CKN',
            'type' => 'RS_SWASTA',
            'license_number' => '445/019/RS-SWASTA/2021',
            'address' => 'Jl. Raden Saleh Raya No. 40, Cikini',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.1917000,
            'longitude' => 106.8423000,
            'cold_chain_ready' => true,
            'contact_person' => 'Apt. Natalia Kristiani, M.Farm',
            'phone' => '021-3899777',
        ]);

        $dinkesDki = Facility::create([
            'name' => 'Dinas Kesehatan Provinsi DKI Jakarta',
            'code' => 'DKS-DKI-PST',
            'type' => 'DINKES',
            'ownership' => 'PEMERINTAH',
            'license_number' => 'GOV-REG-DKI-01',
            'address' => 'Jl. Kesehatan No. 10, Petojo Selatan',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'district' => 'Jakarta Pusat',
            'latitude' => -6.1831000,
            'longitude' => 106.8315000,
            'cold_chain_ready' => true,
            'contact_person' => 'drg. Bambang Sulistyo, M.Kes',
            'phone' => '021-3456789',
            'email' => 'dinkes.dki@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        // Expanded healthcare facilities for diverse category testing
        $rsudJakartaTimur = Facility::create([
            'name' => 'RSUD Dr. Soetomo Jakarta Timur',
            'code' => 'FSK-RSUD-JTM',
            'type' => 'RSUD',
            'ownership' => 'PEMERINTAH',
            'license_number' => '445/003/RSUD-DKI/2024',
            'address' => 'Jl. Basuki Rahmat No. 15, Cipinang Besar Selatan',
            'city' => 'Jakarta Timur',
            'province' => 'DKI Jakarta',
            'district' => 'Jakarta Timur',
            'latitude' => -6.3600000,
            'longitude' => 106.9500000,
            'cold_chain_ready' => true,
            'contact_person' => 'Apt. Rini Dewi, S.Farm',
            'phone' => '021-4890123',
            'email' => 'rsud.jtm@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        $puskesmasMenteng = Facility::create([
            'name' => 'Puskesmas Kecamatan Menteng',
            'code' => 'FSK-PKM-MTG',
            'type' => 'PUSKESMAS',
            'license_number' => '440/020/PKM-JP/2023',
            'address' => 'Jl. Menteng No. 12, Menteng',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'district' => 'Jakarta Pusat',
            'latitude' => -6.1750000,
            'longitude' => 106.8350000,
            'cold_chain_ready' => true,
            'contact_person' => 'dr. Siti Aminah',
            'phone' => '021-3924567',
            'email' => 'puskesmas.menteng@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        $klinikSejahtera = Facility::create([
            'name' => 'Klinik Sejahtera Medika',
            'code' => 'FSK-KLN-SJH',
            'type' => 'KLINIK',
            'license_number' => '503/055/KLN-DKI/2024',
            'address' => 'Jl. Sudirman No. 88, Karet Tengsin',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.2000000,
            'longitude' => 106.8100000,
            'cold_chain_ready' => false,
            'contact_person' => 'dr. Adi Putra',
            'phone' => '021-4889999',
            'email' => 'klinik.sejahtera@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        $rsSwastaHarapan = Facility::create([
            'name' => 'RS Swasta Harapan Sendiri',
            'code' => 'FSK-RS-SWR',
            'type' => 'RS_SWASTA',
            'ownership' => 'SWASTA',
            'license_number' => '445/020/RS-SWASTA/2023',
            'address' => 'Jl. Rasuna Said No. 55, Kuningan',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'district' => 'Jakarta Selatan',
            'latitude' => -6.2400000,
            'longitude' => 106.8300000,
            'cold_chain_ready' => true,
            'contact_person' => 'Apt. Lestari Putri, M.Farm',
            'phone' => '021-5777888',
            'email' => 'rs.harapan@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        $puskesmasSenayan = Facility::create([
            'name' => 'Puskesmas Kecamatan Senayan',
            'code' => 'FSK-PKM-SNY',
            'type' => 'PUSKESMAS',
            'license_number' => '440/025/PKM-JP/2023',
            'address' => 'Jl. Senayan No. 4, Gelora',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'district' => 'Jakarta Selatan',
            'latitude' => -6.2380000,
            'longitude' => 106.8020000,
            'cold_chain_ready' => true,
            'contact_person' => 'dr. Mulyono',
            'phone' => '021-5589012',
            'email' => 'puskesmas.senayan@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        $rsudBogor = Facility::create([
            'name' => 'RSUD Kota Bogor',
            'code' => 'FSK-RSUD-BGR',
            'type' => 'RSUD',
            'ownership' => 'PEMERINTAH',
            'license_number' => '445/010/RSUD-BGR/2022',
            'address' => 'Jl. Sudirman No. 30, Bogor Kota',
            'city' => 'Bogor',
            'province' => 'Jawa Barat',
            'district' => 'Bogor Kota',
            'latitude' => -6.5940000,
            'longitude' => 106.7890000,
            'cold_chain_ready' => true,
            'contact_person' => 'Apt. Fajar Nugraha, S.Farm',
            'phone' => '0251-234567',
            'email' => 'rsud.bogor@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        $dinkesKota = Facility::create([
            'name' => 'Dinas Kesehatan Kota Bogor',
            'code' => 'DKS-BGR-PST',
            'type' => 'DINKES',
            'ownership' => 'PEMERINTAH',
            'license_number' => 'GOV-REG-BGR-01',
            'address' => 'Jl. P. Diponegoro No. 8, Bogor',
            'city' => 'Bogor',
            'province' => 'Jawa Barat',
            'district' => 'Bogor Kota',
            'latitude' => -6.5950000,
            'longitude' => 106.7900000,
            'cold_chain_ready' => true,
            'contact_person' => 'drg. Heri Wibowo, M.Kes',
            'phone' => '0251-345678',
            'email' => 'dinkes.bogor@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        $klinikMitraSehat = Facility::create([
            'name' => 'Klinik Mitra Sehat',
            'code' => 'FSK-KLN-MTS',
            'type' => 'KLINIK',
            'license_number' => '503/060/KLN-JB/2024',
            'address' => 'Jl. P. Sudirman No. 99, Bogor Tengah',
            'city' => 'Bogor',
            'province' => 'Jawa Barat',
            'district' => 'Bogor Tengah',
            'latitude' => -6.6000000,
            'longitude' => 106.7950000,
            'cold_chain_ready' => false,
            'contact_person' => 'dr. Ainun Jariyah',
            'phone' => '0251-987654',
            'email' => 'klinik.mitra@mediloop.test',
            'status' => 'ACTIVE',
            'verified_at' => now(),
        ]);

        // Demo facilities are verified and complete enough for the operational views.
        $demoFacilities = [
            $puskesmasGambir,
            $rsudTanahAbang,
            $puskesmasSawahBesar,
            $puskesmasTanahAbang,
            $klinikHarmoni,
            $rsPgiCikini,
        ];

        foreach ($demoFacilities as $facility) {
            $facility->update([
                'ownership' => in_array($facility->type, ['KLINIK', 'RS_SWASTA'], true) ? 'SWASTA' : 'PEMERINTAH',
                'province' => 'DKI Jakarta',
                'district' => 'Jakarta Pusat',
                'email' => strtolower($facility->code).'@mediloop.test',
                'status' => 'ACTIVE',
                'verified_at' => now(),
            ]);
        }

        // 2. Seed Multi-Role Demonstration Accounts
        $password = Hash::make((string) config('seed.demo_password', config('app.demo_password', 'mediloop#demo2026')));

        User::create([
            'name' => 'Administrator MediLoop',
            'email' => 'admin@mediloop.test',
            'password' => $password,
            'role' => 'SYSADMIN',
            'status' => 'ACTIVE',
            'email_verified_at' => now(),
        ]);

        $donorStaff = User::create([
            'name' => 'Apt. Hendra Wijaya, S.Farm',
            'email' => 'donor@mediloop.test',
            'password' => $password,
            'facility_id' => $rsudTarakan->id,
            'role' => 'FACILITY_STAFF',
            'status' => 'ACTIVE',
            'job_title' => 'Staf Farmasi Rawat Inap & Gudang',
            'license_sip' => 'SIPA.3171.01.2022.0941',
            'email_verified_at' => now(),
        ]);

        $donorApprover = User::create([
            'name' => 'Apt. Budi Santoso, M.Farm',
            'email' => 'donor.head@mediloop.test',
            'password' => $password,
            'facility_id' => $rsudTarakan->id,
            'role' => 'FACILITY_STAFF',
            'status' => 'ACTIVE',
            'job_title' => 'Kepala Instalasi Farmasi RSUD',
            'license_sip' => 'SIPA.3171.01.2018.0122',
            'email_verified_at' => now(),
        ]);

        $recipientStaff = User::create([
            'name' => 'Apt. Dewi Sartika, S.Farm',
            'email' => 'recipient@mediloop.test',
            'password' => $password,
            'facility_id' => $puskesmasGambir->id,
            'role' => 'FACILITY_STAFF',
            'status' => 'ACTIVE',
            'job_title' => 'Apoteker Penanggung Jawab Obat',
            'license_sip' => 'SIPA.3171.02.2023.1104',
            'email_verified_at' => now(),
        ]);

        $recipientApprover = User::create([
            'name' => 'dr. Maria Ulfah',
            'email' => 'recipient.head@mediloop.test',
            'password' => $password,
            'facility_id' => $puskesmasGambir->id,
            'role' => 'FACILITY_STAFF',
            'status' => 'ACTIVE',
            'job_title' => 'Kepala Puskesmas Kecamatan Gambir',
            'license_sip' => 'SIPD.3171.02.2015.0089',
            'email_verified_at' => now(),
        ]);

        $auditor = User::create([
            'name' => 'drg. Bambang Sulistyo, M.Kes',
            'email' => 'auditor@mediloop.test',
            'password' => $password,
            'facility_id' => $dinkesDki->id,
            'role' => 'SYSADMIN',
            'status' => 'ACTIVE',
            'job_title' => 'Koordinator Pengawas Farmasi & Alkes',
            'license_sip' => 'NIP.197905142006041008',
            'email_verified_at' => now(),
        ]);

        // 3. Seed Master Medicines (25 items across all categories)
        $medicinesData = [
            ['Ceftriaxone 1g Injeksi', 'Ceftriaxone Sodium Serbuk Injeksi', 'MED-CFT-1G', 'Antibiotic', 'Vial Injeksi', 'Vial', 45000, false, 'Simpan di bawah suhu 25°C, terlindung dari cahaya langsung.'],
            ['Insulin Glargine 100 IU/ml', 'Insulin Glargine Recombinant Analog', 'MED-INS-GLR', 'Endocrine', 'Cartridge 3ml', 'Cartridge', 185000, true, 'Rantai dingin wajib terjaga 2°C - 8°C. Jangan dibekukan.', 2.0, 8.0],
            ['Amoxicillin 500mg Kaplet', 'Amoxicillin Trihydrate', 'MED-AMX-500', 'Antibiotic', 'Kaplet', 'Box (100 Kaplet)', 65000, false, 'Simpan pada suhu ruang kering.'],
            ['Paracetamol 10mg/ml Infus', 'Paracetamol Infusion 1000mg/100ml', 'MED-PCT-INF', 'Analgesic', 'Botol Infus 100ml', 'Botol', 28000, false, 'Simpan pada suhu ruang sejuk.'],
            ['Oralit 200ml Serbuk', 'Oral Rehydration Salts Formularium', 'MED-ORS-200', 'Emergency', 'Sachet Serbuk', 'Sachet', 1500, false, ''],
            ['Metformin HCl 500mg Tablet', 'Metformin Hydrochloride 500mg', 'GKL0305033410A1', 'Endocrine', 'Tablet Salut', 'Box (100 Tablet)', 32000, false, 'Simpan pada suhu kamar (15-30°C), terlindung dari kelembaban.'],
            ['Amlodipine Besilate 10mg Tablet', 'Amlodipine Besylate 10mg', 'GKL0705041910B1', 'Cardiovascular', 'Tablet', 'Box (30 Tablet)', 24000, false, 'Simpan di tempat kering dan sejuk.'],
            ['Oxytocin 10 IU/ml Injeksi', 'Oxytocin Synthetic Injection', 'GKL9206307843A1', 'Emergency', 'Ampul 1ml', 'Ampul', 18500, true, 'Wajib rantai dingin 2°C - 8°C. Untuk penanganan kegawatdaruratan kebidanan.', 2.0, 8.0],
            ['Azithromycin 500mg Kaplet', 'Azithromycin Dihydrate 500mg', 'GKL1105044817A1', 'Antibiotic', 'Kaplet Salut Selaput', 'Box (20 Kaplet)', 88000, false, 'Simpan di bawah suhu 30°C.'],
            ['Ciprofloxacin 500mg Tablet', 'Ciprofloxacin HCl', 'MED-CIP-500', 'Antibiotic', 'Tablet', 'Box (10)', 55000, false, 'Simpan suhu ruang.'],
            ['Ranitidine 150mg Tablet', 'Ranitidine HCl', 'MED-RAN-150', 'Gastrointestinal', 'Kaplet', 'Box (20)', 12000, false, 'Simpan di tempat kering.'],
            ['Salbutamol 2.5mg Inhalasi', 'Salbutamol Sulfate', 'MED-SLB-INH', 'Respiratory', 'Inhaler', 'Botol', 32000, false, 'Jauhkan dari panas.'],
            ['Furosemide 40mg Tablet', 'Furosemide', 'MED-FRM-40', 'Cardiovascular', 'Tablet', 'Strip (10)', 8500, false, 'Simpan kering.'],
            ['Ambroxol 30mg Sirup', 'Ambroxol HCl', 'MED-AMB-SIR', 'Respiratory', 'Sirup 60ml', 'Botol', 18000, false, 'Simpan suhu ruang.'],
            ['Ibuprofen 400mg Kapsul', 'Ibuprofen', 'MED-IBU-400', 'Analgesic', 'Kapsul', 'Strip (10)', 9500, false, 'Hindari kelembaban.'],
            ['Lisinopril 10mg Tablet', 'Lisinopril Dihydrate', 'MED-LIS-10', 'Cardiovascular', 'Tablet', 'Box (30)', 22000, false, 'Simpan suhu kamar.'],
            ['Clindamycin 300mg Kapsul', 'Clindamycin Phosphate', 'MED-CLN-300', 'Antibiotic', 'Kapsul', 'Strip (6)', 38000, false, 'Simpan suhu ruang kering.'],
            ['Hydrochlorothiazide 25mg', 'HCTZ', 'MED-HCT-25', 'Cardiovascular', 'Tablet', 'Strip (10)', 6500, false, 'Hindari cahaya.'],
            ['Omeprazole 20mg Kapsul', 'Omeprazole', 'MED-OMP-20', 'Gastrointestinal', 'Kapsul', 'Strip (7)', 15000, false, 'Simpan suhu kamar.'],
            ['Prednisolone 5mg Tablet', 'Prednisolone', 'MED-PRD-5', 'Anti-inflammatory', 'Tablet', 'Strip (10)', 4800, false, 'Simpan kering.'],
            ['Nifedipine 10mg Kapsul', 'Nifedipine', 'MED-NIF-10', 'Cardiovascular', 'Kapsul', 'Strip (10)', 11000, false, 'Simpan sejuk.'],
            ['Fluconazole 150mg Kapsul', 'Fluconazole', 'MED-FLC-150', 'Antifungal', 'Kapsul', 'Strip (1)', 25000, false, 'Simpan suhu ruang.'],
            ['Diphenhydramine 25mg Tablet', 'Diphenhydramine HCl', 'MED-DPH-25', 'Respiratory', 'Tablet', 'Strip (6)', 7500, false, 'Simpan kering.'],
            ['Dextromethorphan 15mg Tablet', 'Dextromethorphan HBr', 'MED-DXM-15', 'Respiratory', 'Tablet', 'Strip (10)', 8200, false, 'Simpan suhu ruang.'],
            ['Gliclazide 80mg Tablet', 'Gliclazide', 'MED-GLZ-80', 'Endocrine', 'Tablet', 'Strip (10)', 28000, false, 'Simpan kering.'],
            ['Simvastatin 40mg Tablet', 'Simvastatin', 'MED-SIM-40', 'Cardiovascular', 'Tablet', 'Box (10)', 19500, false, 'Simpan di tempat kering dan sejuk.'],
            ['Lisinopril 5mg Tablet', 'Lisinopril Dihydrate 5mg', 'MED-LIS-5', 'Cardiovascular', 'Tablet', 'Box (30)', 21000, false, 'Simpan suhu kamar.'],
            ['Spironolactone 25mg Kapsul', 'Spironolactone', 'MED-SPR-25', 'Cardiovascular', 'Kapsul', 'Strip (10)', 14000, false, 'Simpan di bawah 25°C, terlindung dari cahaya.'],
            ['Amiodarone HCl 200mg Injeksi', 'Amiodarone Hydrochloride', 'GKL1205051210A1', 'Cardiac', 'Vial Injeksi', 'Vial', 98000, true, 'Rantai dingin wajib 2°C - 8°C. Waspadai hepatotoksisitas.', 2.0, 8.0],
            ['Duloxetine HCl 60mg Kapsul', 'Duloxetine', 'MED-DUL-60', 'Psychiatric', 'Kapsul', 'Box (14)', 42000, false, 'Hindari di tempat lembab dan panas.'],
            ['Bupropion HCl 150mg Tablet', 'Bupropion', 'GKL0705032110A1', 'Psychiatric', 'Tablet', 'Strip (10)', 28000, false, 'Simpan di tempat kering.'],
            ['Ondansetron 8mg/ml Infus', 'Ondansetron HCl Ondansetron Infusion', 'MED-OND-INF', 'GI/Emergency', 'Botol Infus 250ml', 'Botol', 18500, true, 'Jangan campur dengan derivatif asam amino glutamat.'],
            ['Metoclopramide 10mg Infus', 'Metoclopramide HCl', 'MED-MET-INF', 'GI', 'Infus', 'Vial Infus', 12000, false, 'Simpan pada suhu ruang.'],
            ['Antacid Maag Chewable', 'Aluminium Hydroxide+Magnesium Hydroxide', 'MED-ANT-CHW', 'GI', 'Chewable Tablet', 'Box (12)', 5200, false, 'Hindari konsumsi bersama triptan.'],
            ['Metoprolol Succinate 50mg Tablet', 'Metoprolol Succinate', 'MED-METP-50', 'Cardiovascular', 'Tablet', 'Strip (10)', 24000, false, 'Simpan pada suhu kamar.'],
            ['Enalapril Maleate 20mg Tablet', 'Enalaprilat', 'MED-ENAL-20', 'Cardiovascular', 'Tablet', 'Box (30)', 23000, false, 'Simpan suhu ruang.'],
            ['Folic Acid 0.4mg Tablet', 'Folic Acid 0.4 mg', 'MED-FOL-04', 'Maternal', 'Tablet', 'Strip (30)', 1800, false, 'Boleh diberikan bersamaan obat jantung.'],
            ['Iron Fumarate 65mg Tablet', 'Ferrous Fumarate 65mg', 'MED-FEF-65', 'Maternal', 'Tablet', 'Strip (10)', 1200, false, 'Sertakan vitamin C untuk absorbsi optimal.'],
            ['Calcium Glucobay Tablet', 'Calcium Pantothenate 1000mg + Pyridoxine HCl 50mg', 'MED-CALP-1000', 'Maternal', 'Tablet', 'Box (20)', 8500, false, 'Hindari di atas 30°C.'],
            ['Aspirin 81mg Chewable', 'Aspirin Low Dose 81mg', 'MED-ASP-81', 'Analgesic', 'Chewable Tablet', 'Box (50)', 1500, false, 'Batas waktu pakai 30 hari.'],
            ['Acetaminophen 325mg Tablet', 'Paracetamol 325mg', 'MED-APY-325', 'Analgesic', 'Tablet', 'Box (100)', 1800, false, 'Simpan kering.'],
            ['Dexamethasone 0.5mg Tablet', 'Dexamethasone', 'MED-DEX-05', 'Anti-inflammatory', 'Tablet', 'Box (20)', 1400, false, 'Hindari semprot hidung bila laringmatitis.'],
            ['Prednisone 5mg Tablet', 'Prednisone', 'MED-PRED-5', 'Anti-inflammatory', 'Tablet', 'Strip (10)', 3500, false, 'Simpan kering.'],
            ['Methylprednisolon Sulfat 40mg Injeksi', 'Methylprednisolone Sulfate', 'GKL1303050810A1', 'Anti-inflammatory', 'Ampul 1ml', 'Ampul', 3800, true, 'Wajib rantai dingin 2°C - 8°C.', 2.0, 8.0],
            ['Betamethasone Valerate 1mg Tablet', 'Betamethasone valerate', 'MED-BET-1', 'Anti-inflammatory', 'Tablet', 'Strip (20)', 2200, false, 'Jangangunakan luar jangka panjang.'],
            ['Clotrimazole Troches 5mg Tablet', 'Clotrimazole', 'MED-CLT-5', 'Antifungal', 'Troche Tablet', 'Box (80)', 4500, false, 'Berhati-hati perut mulas dan mimisan.'],
            ['Itraconazole Capsules 200mg', 'Itraconazole', 'MED-ITR-200', 'Antifungal', 'Kapsul', 'Box (14)', 68000, true, 'Jangan simpan di daerah lembab.', 2.0, 8.0],
            ['Terconazole 50mg Suppository', 'Terconazole', 'MED-TER-50', 'Antifungal', 'Suppository', 'Box (14)', 21000, false, 'Jangan cuci area genital selama penggunaan.'],
            ['Mupirocin Ointment 2%', 'Mupirocin', 'MED-MRN-2', 'Antibiotic', 'Salep', 'Tube (30g)', 5200, false, 'Simpan tetap terjaga dari kelembaban.'],
            ['Cefazolin 1g Injeksi', 'Cefazolin Sodium', 'MED-CAZ-1G', 'Antibiotic', 'Vial Injeksi', 'Vial', 38000, false, 'Simpan bawah 25°C, hindari cahaya.'],
            ['Linezolid 600mg Tablet', 'Linezolid', 'MED-LNZ-600', 'Antibiotic', 'Tablet', 'Box (14)', 210000, false, 'Simpan pada suhu ruang.'],
            ['Moxifloxacin 400mg Injeksi', 'Moxifloxacin', 'MED-MOX-400', 'Antibiotic', 'Vial Injeksi', 'Vial', 185000, false, 'Simpan bawah 25°C.'],
        ];

        $medicineRefs = [];
        foreach ($medicinesData as $m) {
            $data = [
                'name' => $m[0],
                'generic_name' => $m[1],
                'sku' => $m[2],
                'category' => $m[3],
                'dosage_form' => $m[4],
                'unit' => $m[5],
                'unit_price_idr' => $m[6],
                'requires_cold_chain' => $m[7],
                'handling_instructions' => $m[8],
            ];
            if (isset($m[9])) {
                $data['storage_min_temp'] = $m[9];
            }
            if (isset($m[10])) {
                $data['storage_max_temp'] = $m[10];
            }
            $medicineRefs[(string) $data['sku']] = Medicine::create($data);
        }

        // Reference medicines by SKU for batches
        $ceftriaxone = $medicineRefs['MED-CFT-1G'];
        $insulin = $medicineRefs['MED-INS-GLR'];
        $amoxicillin = $medicineRefs['MED-AMX-500'];
        $paracetamol = $medicineRefs['MED-PCT-INF'];
        $oxytocin = $medicineRefs['GKL9206307843A1'];
        $azithromycin = $medicineRefs['GKL1105044817A1'];

        // 4. Seed Batches at RSUD Tarakan (Donor)
        // Canonical PRD Benchmark Batch: Stock 480, Burn 4/day, Expiry in 45 days -> 300 Projected Waste, High Risk!
        $benchmarkBatch = Batch::create([
            'facility_id' => $rsudTarakan->id,
            'medicine_id' => $ceftriaxone->id,
            'batch_number' => 'CFT-2026-X89',
            'current_stock' => 480,
            'daily_burn_rate' => 4.0,
            'expiry_date' => Carbon::today()->addDays(45)->toDateString(),
            'unit_price_idr' => 45000,
            'storage_location' => 'Gudang Farmasi RSUD - Rak Injeksi B-02',
            'status' => 'ACTIVE',
        ]);

        $insulinBatch = Batch::create([
            'facility_id' => $rsudTarakan->id,
            'medicine_id' => $insulin->id,
            'batch_number' => 'INS-2026-B12',
            'current_stock' => 120,
            'daily_burn_rate' => 1.0,
            'expiry_date' => Carbon::today()->addDays(28)->toDateString(),
            'unit_price_idr' => 185000,
            'storage_location' => 'Cold Room RSUD - Rak Chiller A-1',
            'status' => 'ACTIVE',
        ]);

        $amoxicillinBatch = Batch::create([
            'facility_id' => $rsudTarakan->id,
            'medicine_id' => $amoxicillin->id,
            'batch_number' => 'AMX-2026-K44',
            'current_stock' => 300,
            'daily_burn_rate' => 8.0,
            'expiry_date' => Carbon::today()->addDays(80)->toDateString(),
            'unit_price_idr' => 65000,
            'storage_location' => 'Gudang Farmasi RSUD - Rak Tablet C-04',
            'status' => 'ACTIVE',
        ]);

        $paracetamolBatch = Batch::create([
            'facility_id' => $rsudTarakan->id,
            'medicine_id' => $paracetamol->id,
            'batch_number' => 'PCT-2026-M55',
            'current_stock' => 200,
            'daily_burn_rate' => 2.5,
            'expiry_date' => Carbon::today()->addDays(50)->toDateString(),
            'unit_price_idr' => 28000,
            'storage_location' => 'Gudang Farmasi RSUD - Rak Cairan D-01',
            'status' => 'ACTIVE',
        ]);

        // Seed Recipient Stock (showing urgent shortage at Puskesmas Gambir)
        Batch::create([
            'facility_id' => $puskesmasGambir->id,
            'medicine_id' => $ceftriaxone->id,
            'batch_number' => 'CFT-2025-OLD',
            'current_stock' => 12, // Critical shortage (only 2 days left)
            'daily_burn_rate' => 6.0,
            'expiry_date' => Carbon::today()->addDays(120)->toDateString(),
            'unit_price_idr' => 45000,
            'storage_location' => 'Apotek Puskesmas Gambir',
            'status' => 'ACTIVE',
        ]);

        // Seed Realistic Partner Facility Inventories (showing distinct genuine clinical shortages across Jakarta)
        // 1. RSUD Tanah Abang: Critical shortage for Insulin (3 days left) & Paracetamol Infus (2 days left), Surplus Ceftriaxone & Amoxicillin
        Batch::create([
            'facility_id' => $rsudTanahAbang->id,
            'medicine_id' => $insulin->id,
            'batch_number' => 'INS-2026-TNA',
            'current_stock' => 12,
            'daily_burn_rate' => 4.0,
            'expiry_date' => Carbon::today()->addDays(180)->toDateString(),
            'unit_price_idr' => 185000,
            'storage_location' => 'Chiller Farmasi RSUD Tanah Abang',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $rsudTanahAbang->id,
            'medicine_id' => $paracetamol->id,
            'batch_number' => 'PCT-2026-TNA',
            'current_stock' => 16,
            'daily_burn_rate' => 8.0,
            'expiry_date' => Carbon::today()->addDays(200)->toDateString(),
            'unit_price_idr' => 28000,
            'storage_location' => 'Gudang Farmasi IGD RSUD Tanah Abang',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $rsudTanahAbang->id,
            'medicine_id' => $ceftriaxone->id,
            'batch_number' => 'CFT-2026-TNA',
            'current_stock' => 320,
            'daily_burn_rate' => 8.0,
            'expiry_date' => Carbon::today()->addDays(180)->toDateString(),
            'unit_price_idr' => 45000,
            'storage_location' => 'Gudang Utama RSUD Tanah Abang',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $rsudTanahAbang->id,
            'medicine_id' => $amoxicillin->id,
            'batch_number' => 'AMX-2026-TNA',
            'current_stock' => 500,
            'daily_burn_rate' => 10.0,
            'expiry_date' => Carbon::today()->addDays(220)->toDateString(),
            'unit_price_idr' => 65000,
            'storage_location' => 'Gudang Farmasi RSUD Tanah Abang',
            'status' => 'ACTIVE',
        ]);

        // 2. Puskesmas Sawah Besar: Critical shortage for Amoxicillin (2 days left), Urgent Ceftriaxone (5 days), Moderate Insulin (8 days)
        Batch::create([
            'facility_id' => $puskesmasSawahBesar->id,
            'medicine_id' => $amoxicillin->id,
            'batch_number' => 'AMX-2026-SWB',
            'current_stock' => 30,
            'daily_burn_rate' => 15.0,
            'expiry_date' => Carbon::today()->addDays(150)->toDateString(),
            'unit_price_idr' => 65000,
            'storage_location' => 'Apotek Rawat Jalan PKM Sawah Besar',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $puskesmasSawahBesar->id,
            'medicine_id' => $ceftriaxone->id,
            'batch_number' => 'CFT-2026-SWB',
            'current_stock' => 25,
            'daily_burn_rate' => 5.0,
            'expiry_date' => Carbon::today()->addDays(160)->toDateString(),
            'unit_price_idr' => 45000,
            'storage_location' => 'Ruang Tindakan PKM Sawah Besar',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $puskesmasSawahBesar->id,
            'medicine_id' => $insulin->id,
            'batch_number' => 'INS-2026-SWB',
            'current_stock' => 16,
            'daily_burn_rate' => 2.0,
            'expiry_date' => Carbon::today()->addDays(180)->toDateString(),
            'unit_price_idr' => 185000,
            'storage_location' => 'Cold Box Prolanis PKM Sawah Besar',
            'status' => 'ACTIVE',
        ]);

        // 3. RS PGI Cikini: Critical shortage for Ceftriaxone (2 days left) & Paracetamol Infus (5 days left), Surplus Insulin (40 days)
        Batch::create([
            'facility_id' => $rsPgiCikini->id,
            'medicine_id' => $ceftriaxone->id,
            'batch_number' => 'CFT-2026-CKN',
            'current_stock' => 20,
            'daily_burn_rate' => 10.0,
            'expiry_date' => Carbon::today()->addDays(180)->toDateString(),
            'unit_price_idr' => 45000,
            'storage_location' => 'Depo Farmasi Bedah Sentral RS Cikini',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $rsPgiCikini->id,
            'medicine_id' => $paracetamol->id,
            'batch_number' => 'PCT-2026-CKN',
            'current_stock' => 30,
            'daily_burn_rate' => 6.0,
            'expiry_date' => Carbon::today()->addDays(200)->toDateString(),
            'unit_price_idr' => 28000,
            'storage_location' => 'Depo Rawat Inap RS Cikini',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $rsPgiCikini->id,
            'medicine_id' => $insulin->id,
            'batch_number' => 'INS-2026-CKN',
            'current_stock' => 120,
            'daily_burn_rate' => 3.0,
            'expiry_date' => Carbon::today()->addDays(210)->toDateString(),
            'unit_price_idr' => 185000,
            'storage_location' => 'Kulkas Farmasi Pusat RS Cikini',
            'status' => 'ACTIVE',
        ]);

        // 4. Puskesmas Tanah Abang: Critical shortage for Azithromycin (2 days left) & Insulin (3.3 days left)
        Batch::create([
            'facility_id' => $puskesmasTanahAbang->id,
            'medicine_id' => $azithromycin->id,
            'batch_number' => 'AZM-2026-PTA',
            'current_stock' => 12,
            'daily_burn_rate' => 6.0,
            'expiry_date' => Carbon::today()->addDays(150)->toDateString(),
            'unit_price_idr' => 88000,
            'storage_location' => 'Poli ISPA PKM Tanah Abang',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $puskesmasTanahAbang->id,
            'medicine_id' => $insulin->id,
            'batch_number' => 'INS-2026-PTA',
            'current_stock' => 5,
            'daily_burn_rate' => 1.5,
            'expiry_date' => Carbon::today()->addDays(180)->toDateString(),
            'unit_price_idr' => 185000,
            'storage_location' => 'Chiller Poli PKM Tanah Abang',
            'status' => 'ACTIVE',
        ]);
        Batch::create([
            'facility_id' => $puskesmasTanahAbang->id,
            'medicine_id' => $amoxicillin->id,
            'batch_number' => 'AMX-2026-PTA',
            'current_stock' => 60,
            'daily_burn_rate' => 10.0,
            'expiry_date' => Carbon::today()->addDays(160)->toDateString(),
            'unit_price_idr' => 65000,
            'storage_location' => 'Apotek PKM Tanah Abang',
            'status' => 'ACTIVE',
        ]);

        // 5. Klinik Harmoni: Oral medicines only (no cold chain)
        Batch::create([
            'facility_id' => $klinikHarmoni->id,
            'medicine_id' => $amoxicillin->id,
            'batch_number' => 'AMX-2026-HRM',
            'current_stock' => 70,
            'daily_burn_rate' => 7.0,
            'expiry_date' => Carbon::today()->addDays(140)->toDateString(),
            'unit_price_idr' => 65000,
            'storage_location' => 'Apotek Klinik Harmoni',
            'status' => 'ACTIVE',
        ]);

        // 5. Seed Demonstration Transfers across States
        // Completed Transfer: RSUD to Puskesmas Gambir
        $completedTransfer = Transfer::create([
            'transfer_code' => 'TRF-202610-001',
            'donor_facility_id' => $rsudTarakan->id,
            'recipient_facility_id' => $puskesmasGambir->id,
            'batch_id' => $benchmarkBatch->id,
            'quantity' => 100,
            'status' => 'COMPLETED',
            'need_score' => 95.0,
            'proximity_score' => 96.4,
            'logistics_score' => 90.0,
            'match_score' => 94.4,
            'distance_km' => 1.82,
            'recipient_daily_burn_rate' => 6,
            'recipient_intake_cap' => 228,
            'tracking_number' => 'MED-EXP-20261018-01',
            'courier_type' => 'Kurir Khusus Farmasi Berpendingin',
            'notes' => 'Redistribusi darurat untuk mencegah kekosongan antibiotik ceftriaxone di Puskesmas Gambir.',
            'proposed_by' => $donorStaff->id,
            'approved_by' => $donorApprover->id,
            'accepted_by' => $recipientStaff->id,
            'completed_by' => $recipientApprover->id,
            'proposed_at' => Carbon::now()->subDays(2),
            'approved_at' => Carbon::now()->subDays(2)->addHours(2),
            'accepted_at' => Carbon::now()->subDays(1),
            'shipped_at' => Carbon::now()->subDays(1)->addHours(3),
            'completed_at' => Carbon::now()->subHours(4),
        ]);

        // Audit Log for completed transfer
        AuditLog::create([
            'actor_id' => $recipientApprover->id,
            'facility_id' => $puskesmasGambir->id,
            'transfer_id' => $completedTransfer->id,
            'action' => 'TRANSFER_COMPLETED',
            'description' => 'Kepala Puskesmas Gambir mengonfirmasi penerimaan fisik 100 vial Ceftriaxone. Stok diperbarui secara atomik.',
            'state_delta' => [
                'donor_facility' => 'RSUD Tarakan',
                'recipient_facility' => 'Puskesmas Gambir',
                'quantity' => 100,
                'status' => 'COMPLETED',
            ],
            'created_at' => Carbon::now()->subHours(4),
        ]);

        // In Transit Transfer: Insulin RSUD to Puskesmas
        Transfer::create([
            'transfer_code' => 'TRF-202610-002',
            'donor_facility_id' => $rsudTarakan->id,
            'recipient_facility_id' => $puskesmasGambir->id,
            'batch_id' => $insulinBatch->id,
            'quantity' => 40,
            'status' => 'IN_TRANSIT',
            'need_score' => 95.0,
            'proximity_score' => 96.4,
            'logistics_score' => 95.0,
            'match_score' => 95.4,
            'distance_km' => 1.82,
            'recipient_daily_burn_rate' => 2,
            'recipient_intake_cap' => 42,
            'tracking_number' => 'COLD-BOX-009',
            'courier_type' => 'Cold-Chain Mobile Unit Dinkes',
            'notes' => 'Redistribusi Insulin Glargine dengan box berpendingin dan data logger suhu.',
            'proposed_by' => $donorStaff->id,
            'approved_by' => $donorApprover->id,
            'accepted_by' => $recipientStaff->id,
            'proposed_at' => Carbon::now()->subHours(8),
            'approved_at' => Carbon::now()->subHours(6),
            'accepted_at' => Carbon::now()->subHours(3),
            'shipped_at' => Carbon::now()->subHour(),
        ]);
    }
}
