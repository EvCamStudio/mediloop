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
        $password = Hash::make((string) config('app.demo_password', 'password'));

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
            'role' => 'FACILITY_APPROVER',
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
            'role' => 'FACILITY_APPROVER',
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
            'role' => 'DINKES_AUDITOR',
            'status' => 'ACTIVE',
            'job_title' => 'Koordinator Pengawas Farmasi & Alkes',
            'license_sip' => 'NIP.197905142006041008',
            'email_verified_at' => now(),
        ]);

        // 3. Seed Master Medicines
        $ceftriaxone = Medicine::create([
            'name' => 'Ceftriaxone 1g Injeksi',
            'generic_name' => 'Ceftriaxone Sodium Serbuk Injeksi',
            'sku' => 'MED-CFT-1G',
            'category' => 'Antibiotic',
            'dosage_form' => 'Vial Injeksi',
            'unit' => 'Vial',
            'unit_price_idr' => 45000,
            'requires_cold_chain' => false,
            'handling_instructions' => 'Simpan di bawah suhu 25°C, terlindung dari cahaya langsung.',
        ]);

        $insulin = Medicine::create([
            'name' => 'Insulin Glargine 100 IU/ml',
            'generic_name' => 'Insulin Glargine Recombinant Analog',
            'sku' => 'MED-INS-GLR',
            'category' => 'Endocrine',
            'dosage_form' => 'Cartridge 3ml',
            'unit' => 'Cartridge',
            'unit_price_idr' => 185000,
            'requires_cold_chain' => true,
            'storage_min_temp' => 2.0,
            'storage_max_temp' => 8.0,
            'handling_instructions' => 'Rantai dingin wajib terjaga 2°C - 8°C. Jangan dibekukan.',
        ]);

        $amoxicillin = Medicine::create([
            'name' => 'Amoxicillin 500mg Kaplet',
            'generic_name' => 'Amoxicillin Trihydrate',
            'sku' => 'MED-AMX-500',
            'category' => 'Antibiotic',
            'dosage_form' => 'Kaplet',
            'unit' => 'Box (100 Kaplet)',
            'unit_price_idr' => 65000,
            'requires_cold_chain' => false,
            'handling_instructions' => 'Simpan pada suhu ruang kering.',
        ]);

        $paracetamol = Medicine::create([
            'name' => 'Paracetamol 10mg/ml Infus',
            'generic_name' => 'Paracetamol Infusion 1000mg/100ml',
            'sku' => 'MED-PCT-INF',
            'category' => 'Analgesic',
            'dosage_form' => 'Botol Infus 100ml',
            'unit' => 'Botol',
            'unit_price_idr' => 28000,
            'requires_cold_chain' => false,
            'handling_instructions' => 'Simpan pada suhu ruang sejuk.',
        ]);

        $oralit = Medicine::create([
            'name' => 'Oralit 200ml Serbuk',
            'generic_name' => 'Oral Rehydration Salts Formularium',
            'sku' => 'MED-ORS-200',
            'category' => 'Emergency',
            'dosage_form' => 'Sachet Serbuk',
            'unit' => 'Sachet',
            'unit_price_idr' => 1500,
            'requires_cold_chain' => false,
        ]);

        $metformin = Medicine::create([
            'name' => 'Metformin HCl 500mg Tablet',
            'generic_name' => 'Metformin Hydrochloride 500mg',
            'sku' => 'GKL0305033410A1',
            'category' => 'Endocrine',
            'dosage_form' => 'Tablet Salut',
            'unit' => 'Box (100 Tablet)',
            'unit_price_idr' => 32000,
            'requires_cold_chain' => false,
            'handling_instructions' => 'Simpan pada suhu kamar (15-30°C), terlindung dari kelembaban.',
        ]);

        $amlodipine = Medicine::create([
            'name' => 'Amlodipine Besilate 10mg Tablet',
            'generic_name' => 'Amlodipine Besylate 10mg',
            'sku' => 'GKL0705041910B1',
            'category' => 'Cardiovascular',
            'dosage_form' => 'Tablet',
            'unit' => 'Box (30 Tablet)',
            'unit_price_idr' => 24000,
            'requires_cold_chain' => false,
            'handling_instructions' => 'Simpan di tempat kering dan sejuk.',
        ]);

        $oxytocin = Medicine::create([
            'name' => 'Oxytocin 10 IU/ml Injeksi',
            'generic_name' => 'Oxytocin Synthetic Injection',
            'sku' => 'GKL9206307843A1',
            'category' => 'Emergency',
            'dosage_form' => 'Ampul 1ml',
            'unit' => 'Ampul',
            'unit_price_idr' => 18500,
            'requires_cold_chain' => true,
            'storage_min_temp' => 2.0,
            'storage_max_temp' => 8.0,
            'handling_instructions' => 'Wajib rantai dingin 2°C - 8°C. Untuk penanganan kegawatdaruratan kebidanan.',
        ]);

        $azithromycin = Medicine::create([
            'name' => 'Azithromycin 500mg Kaplet',
            'generic_name' => 'Azithromycin Dihydrate 500mg',
            'sku' => 'GKL1105044817A1',
            'category' => 'Antibiotic',
            'dosage_form' => 'Kaplet Salut Selaput',
            'unit' => 'Box (20 Kaplet)',
            'unit_price_idr' => 88000,
            'requires_cold_chain' => false,
            'handling_instructions' => 'Simpan di bawah suhu 30°C.',
        ]);

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
