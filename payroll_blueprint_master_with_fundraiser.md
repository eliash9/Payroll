# MASTER PAYROLL & HRIS BLUEPRINT (WITH FUNDRAISER MODE)

> File ini menggabungkan:
> 1. Blueprint ringkas (business-level)
> 2. Blueprint teknis (DB + API + algoritma)
> 3. Blueprint khusus Fundraiser/Relawan (crowdfunding/LAZ/NGO)

Catatan sumber: gunakan berkas ini sebagai referensi utama (versi bersih ASCII, penomoran modul konsisten).

---

## BAGIAN A - OVERVIEW & MODUL UTAMA

# Payroll & HRIS Integrated System - Full Blueprint

## 1. Project Overview
Sistem Payroll & HRIS terintegrasi yang mencakup:
- Manajemen karyawan
- Absensi (fingerprint, mobile app, manual)
- Cuti/Izin/Sakit
- Lembur
- Payroll Engine
- BPJS Ketenagakerjaan & Kesehatan
- PPh21
- Slip Gaji & Self-Service
- Laporan payroll, absensi, BPJS, pajak
- KPI & Performance-based pay (insentif, target, bonus)

Target:
- Perusahaan kecil-menengah
- Multi-cabang
- Web & Mobile ready

---

## 2. System Modules

### 2.1 Master Data Karyawan
- Data personal
- Employment data
- Payroll data
- BPJS data
- Pajak/PTKP
- Dokumen kerja

---

### 2.2 Absensi & Shift
- Master shift
- Jadwal kerja karyawan
- Absensi: fingerprint/mobile/manual
- Rekap absensi bulanan

---

### 2.3 Cuti / Izin / Sakit
- Master cuti
- Pengajuan
- Approval
- Sinkron ke absensi

---

### 2.4 Lembur
- Pengajuan lembur
- Approval
- Tarif lembur
- Rekap lembur bulanan

---

### 2.5 Payroll Components
- Pendapatan & potongan
- Komponen tetap & variabel
- Rumus kalkulasi
- PPh21 applicable
- Mapping ke karyawan

---

### 2.6 Integrasi BPJS
- Parameter tarif
- Kalkulasi BPJS Kesehatan & TK
- Potongan karyawan dan porsi perusahaan
- Laporan BPJS

---

### 2.7 Pajak PPh21
- PTKP
- Tarif progresif
- PKP
- PPh21 bulanan
- Bukti potong

---

### 2.8 Pinjaman & Potongan Lain
- Pinjaman karyawan
- Tenor & saldo
- Potong otomatis payroll

---

### 2.9 Payroll Engine
- Input: absensi, lembur, BPJS, pajak, komponen variabel
- Output: gaji bruto, potongan, take home pay
- Flow: lock absensi -> generate payroll -> approval -> export

---

### 2.10 Slip Gaji & Portal
- Slip PDF
- Portal karyawan
- Riwayat absensi, cuti, payroll

---

### 2.11 Reporting & Dashboard
- Laporan payroll
- Laporan absensi
- BPJS & pajak
- Dashboard kehadiran

---

### 2.12 KPI & Performance-Based Pay (NEW)

#### 2.12.1 Master KPI
- Nama KPI
- Tipe nilai
- Target default
- Bobot default
- Periode: bulanan/mingguan/kuartal/tahun
- Kategori: individu/tim/divisi

#### 2.12.2 KPI Assignment
- KPI per karyawan
- Target khusus
- Bobot khusus
- Periode aktif

#### 2.12.3 Input Pencapaian
- Manual supervisor
- Import Excel
- Integrasi API (Sales/CRM/POS)

#### 2.12.4 Kalkulasi Insentif & Komisi
Contoh rumus:
```
komisi = actual_units * komisi_per_unit
```
```
if achievement >= 120%: bonus = max_bonus * 1.2
elif achievement >=100%: bonus = max_bonus
elif achievement >=80%: bonus = max_bonus * 0.5
else: bonus = 0
```

#### 2.12.5 Integrasi ke Payroll Engine
- Komponen: Insentif KPI, Komisi, Bonus Target
- Otomatis masuk payroll_details & slip gaji

---

## 3. Database Structure (ERD Blueprint)

### Employee Tables
- employees
- employee_contracts
- employee_payroll_components
- employee_bpjs
- employee_tax_profiles

### Attendance Tables
- shifts
- employee_schedules
- attendance_logs
- attendance_summaries

### Leave & Overtime
- leave_types
- leave_requests
- overtime_requests

### Payroll
- payroll_periods
- payroll_components
- payroll_details
- employee_loans
- employee_loan_schedules

### KPI Tables
- kpi_master
- employee_kpi_assignments
- employee_kpi_results
- kpi_payroll_mapping

---

## 4. Monthly Payroll Cycle
1. Set jadwal kerja
2. Rekap absensi
3. Rekap lembur
4. Hitung BPJS
5. Hitung PPh21
6. Hitung KPI & insentif
7. Generate payroll
8. Approval
9. Slip gaji & laporan

---

## 5. Technical Requirements
- Backend: Laravel / Node / Go / Python
- API: REST/GraphQL
- Queue system untuk payroll
- Frontend: Tailwind, React, Vue, Blade
- Integrasi: fingerprint, WhatsApp API
- Timezone: Asia/Jakarta
- Currency: IDR


---

## BAGIAN B - BLUEPRINT TEKNIS (DATABASE, API, ALGORITMA)

# Payroll & HRIS Integrated System - Technical Blueprint (v2)

> Fokus: struktur database, relasi, dan sedikit hint API.  
> Asumsi DB: **PostgreSQL / MySQL** (relasional biasa).  
> Timezone default: `Asia/Jakarta`, currency: `IDR`.

---

## 0. Konvensi Teknis

### 0.1 Konvensi Penamaan
- Nama tabel: `snake_case`, jamak -> `employees`, `attendance_logs`
- Primary key: `id` (BIGINT UNSIGNED AUTO_INCREMENT atau BIGSERIAL)
- Foreign key: `{related_singular}_id`
- Timestamp standar: `created_at`, `updated_at`, optional `deleted_at` (soft delete)

### 0.2 Tipe Data Umum
- `BIGINT` / `BIGINT UNSIGNED` -> id
- `VARCHAR(191)` -> teks pendek (nama, kode, dsb.)
- `TEXT` -> teks panjang (deskripsi, catatan)
- `DECIMAL(15,2)` -> uang
- `DECIMAL(10,2)` -> persentase/angka metrik
- `DATE` -> tanggal
- `DATETIME` / `TIMESTAMP` -> datetime
- `ENUM` -> bisa diganti `VARCHAR` + constraint di level aplikasi

---

## 1. Master Data & Struktur Organisasi

### 1.1 Tabel `companies`
Jika mau multi-perusahaan 1 database.

- `id` BIGINT PK
- `name` VARCHAR(191) NOT NULL
- `code` VARCHAR(50) UNIQUE
- `address` TEXT NULL
- `phone` VARCHAR(50) NULL
- `email` VARCHAR(191) NULL
- `npwp` VARCHAR(64) NULL
- `created_at`, `updated_at`

---

### 1.2 Tabel `branches`
- `id` BIGINT PK
- `company_id` BIGINT FK -> `companies.id`
- `name` VARCHAR(191) NOT NULL
- `code` VARCHAR(50) UNIQUE
- `address` TEXT NULL
- `phone` VARCHAR(50) NULL
- `created_at`, `updated_at`

Index:
- `idx_branches_company_id` (company_id)

---

### 1.3 Tabel `departments`
- `id` BIGINT PK
- `company_id` BIGINT FK -> `companies.id`
- `name` VARCHAR(191) NOT NULL
- `code` VARCHAR(50) UNIQUE
- `created_at`, `updated_at`

---

### 1.4 Tabel `positions`
- `id` BIGINT PK
- `company_id` BIGINT FK -> `companies.id`
- `name` VARCHAR(191) NOT NULL        -- contoh: Software Engineer
- `grade` VARCHAR(50) NULL            -- contoh: Junior, Senior, 1A, 2B
- `created_at`, `updated_at`

---

### 1.5 Tabel `employees`
- `id` BIGINT PK
- `company_id` BIGINT FK -> `companies.id`
- `branch_id` BIGINT FK -> `branches.id` NULL
- `department_id` BIGINT FK -> `departments.id` NULL
- `position_id` BIGINT FK -> `positions.id` NULL

Data personal:
- `employee_code` VARCHAR(50) UNIQUE NOT NULL    -- NIK internal / NIP
- `full_name` VARCHAR(191) NOT NULL
- `nickname` VARCHAR(100) NULL
- `national_id_number` VARCHAR(32) NULL          -- NIK KTP
- `family_card_number` VARCHAR(32) NULL          -- No KK
- `birth_place` VARCHAR(100) NULL
- `birth_date` DATE NULL
- `gender` ENUM('male','female') NULL
- `marital_status` ENUM('single','married','divorced','widowed') NULL
- `dependents_count` TINYINT NULL

Kontak & alamat:
- `phone` VARCHAR(50) NULL
- `email` VARCHAR(191) NULL
- `address` TEXT NULL

Status kerja:
- `employment_type` ENUM('permanent','contract','intern','outsourcing') NOT NULL
- `status` ENUM('active','inactive','suspended','terminated') DEFAULT 'active'
- `join_date` DATE NULL
- `end_date` DATE NULL

Payroll basic:
- `basic_salary` DECIMAL(15,2) DEFAULT 0
- `payroll_type` ENUM('monthly','daily','hourly','commission') DEFAULT 'monthly'
- `bank_name` VARCHAR(100) NULL
- `bank_account_number` VARCHAR(100) NULL
- `bank_account_holder` VARCHAR(191) NULL

- `created_at`, `updated_at`, `deleted_at` NULL

Index:
- `idx_employees_company` (company_id, employee_code)

---

### 1.6 Tabel `employee_contracts`
- `id` BIGINT PK
- `employee_id` BIGINT FK -> `employees.id`
- `contract_number` VARCHAR(100) NULL
- `start_date` DATE NOT NULL
- `end_date` DATE NULL           -- null = tetap
- `contract_type` ENUM('permanent','fixed_term','probation') NOT NULL
- `description` TEXT NULL
- `created_at`, `updated_at`

---

### 1.7 Tabel `employee_tax_profiles`
- `id` BIGINT PK
- `employee_id` BIGINT FK -> `employees.id`
- `tax_id_number` VARCHAR(50) NULL        -- NPWP
- `ptkp_status` VARCHAR(20) NOT NULL      -- TK/0, TK/1, K/0, K/1, ...
- `is_tax_bruto` TINYINT(1) DEFAULT 1     -- bruto/neto scheme
- `created_at`, `updated_at`

---

## 2. BPJS & Benefit

### 2.1 Tabel `employee_bpjs`
- `id` BIGINT PK
- `employee_id` BIGINT FK -> `employees.id`
- `bpjs_kesehatan_number` VARCHAR(50) NULL
- `bpjs_kesehatan_class` VARCHAR(10) NULL
- `bpjs_ketenagakerjaan_number` VARCHAR(50) NULL
- `start_date` DATE NULL
- `is_active` TINYINT(1) DEFAULT 1
- `created_at`, `updated_at`

---

### 2.2 Tabel `bpjs_rates`
Tarif dan parameter tiap program.

- `id` BIGINT PK
- `company_id` BIGINT FK -> `companies.id`
- `program` ENUM('bpjs_kesehatan','jht','jkk','jkm','jp') NOT NULL
- `employee_rate` DECIMAL(5,2) NOT NULL    -- persen % karyawan
- `employer_rate` DECIMAL(5,2) NOT NULL    -- persen % perusahaan
- `salary_cap_min` DECIMAL(15,2) NULL
- `salary_cap_max` DECIMAL(15,2) NULL
- `effective_from` DATE NOT NULL
- `effective_to` DATE NULL
- `created_at`, `updated_at`

Rule:
- Saat hitung payroll, ambil record `bpjs_rates` aktif berdasarkan tanggal periode.

---

## 3. Absensi & Shift

### 3.1 Tabel `shifts`
- `id` BIGINT PK
- `company_id` BIGINT FK -> `companies.id`
- `name` VARCHAR(100) NOT NULL
- `code` VARCHAR(50) UNIQUE
- `start_time` TIME NOT NULL
- `end_time` TIME NOT NULL
- `tolerance_late_minutes` INT DEFAULT 0
- `tolerance_early_leave_minutes` INT DEFAULT 0
- `is_night_shift` TINYINT(1) DEFAULT 0
- `created_at`, `updated_at`

---

### 3.2 Tabel `employee_schedules`
Jadwal kerja per hari.

- `id` BIGINT PK
- `employee_id` BIGINT FK -> `employees.id`
- `shift_id` BIGINT FK -> `shifts.id`
- `work_date` DATE NOT NULL
- `is_day_off` TINYINT(1) DEFAULT 0
- `created_at`, `updated_at`

Index:
- `idx_employee_schedules_emp_date` (employee_id, work_date)

---

### 3.3 Tabel `attendance_logs`
Log mentah absen.

- `id` BIGINT PK
- `employee_id` BIGINT FK -> `employees.id`
- `device_id` VARCHAR(100) NULL                -- ID mesin absensi / source
- `scan_time` DATETIME NOT NULL
- `scan_type` ENUM('in','out') NOT NULL
- `source` ENUM('device','mobile','web','import') NOT NULL
- `lat` DECIMAL(10,6) NULL                     -- jika mobile
- `lng` DECIMAL(10,6) NULL
- `photo_path` VARCHAR(255) NULL               -- selfie path jika ada
- `created_at`, `updated_at`

Index:
- `idx_attendance_logs_emp_scan` (employee_id, scan_time)

---

### 3.4 Tabel `attendance_summaries`
Rekap absensi per hari per karyawan.

- `id` BIGINT PK
- `employee_id` BIGINT FK -> `employees.id`
- `work_date` DATE NOT NULL
- `shift_id` BIGINT FK -> `shifts.id` NULL
- `check_in` DATETIME NULL
- `check_out` DATETIME NULL
- `status` ENUM('present','late','early_leave','absent','leave','sick','off','wfh') NOT NULL
- `late_minutes` INT DEFAULT 0
- `early_leave_minutes` INT DEFAULT 0
- `worked_minutes` INT DEFAULT 0
- `overtime_minutes` INT DEFAULT 0
- `remarks` TEXT NULL
- `locked` TINYINT(1) DEFAULT 0             -- dikunci saat payroll

Index:
- `idx_attendance_summaries_emp_date` (employee_id, work_date)

---

## 4. Cuti, Izin, Sakit, Lembur

### 4.1 Tabel `leave_types`
- `id` BIGINT PK
- `company_id` BIGINT FK
- `name` VARCHAR(100) NOT NULL
- `code` VARCHAR(50) UNIQUE
- `is_paid` TINYINT(1) DEFAULT 1
- `is_annual_quota` TINYINT(1) DEFAULT 0    -- memotong jatah cuti tahunan
- `default_quota_days` DECIMAL(5,2) NULL    -- jika tahunan
- `created_at`, `updated_at`

---

### 4.2 Tabel `leave_requests`
- `id` BIGINT PK
- `employee_id` BIGINT FK
- `leave_type_id` BIGINT FK
- `start_date` DATE NOT NULL
- `end_date` DATE NOT NULL
- `total_days` DECIMAL(5,2) NOT NULL
- `reason` TEXT NULL
- `status` ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending'
- `approver_id` BIGINT FK -> `employees.id` NULL
- `approved_at` DATETIME NULL
- `created_at`, `updated_at`

---

### 4.3 Tabel `overtime_requests`
- `id` BIGINT PK
- `employee_id` BIGINT FK
- `work_date` DATE NOT NULL
- `start_time` DATETIME NOT NULL
- `end_time` DATETIME NOT NULL
- `total_minutes` INT NOT NULL
- `reason` TEXT NULL
- `status` ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending'
- `approver_id` BIGINT FK -> `employees.id` NULL
- `approved_at` DATETIME NULL
- `created_at`, `updated_at`

---

### 4.4 Tabel `overtime_policies`
Aturan lembur (opsional, jika mau kompleks).

- `id` BIGINT PK
- `company_id` BIGINT FK
- `name` VARCHAR(100)
- `description` TEXT NULL
- `weekday_rate` DECIMAL(5,2) NULL        -- kali dari hourly_rate
- `weekend_rate` DECIMAL(5,2) NULL
- `holiday_rate` DECIMAL(5,2) NULL
- `effective_from` DATE NOT NULL
- `effective_to` DATE NULL
- `created_at`, `updated_at`

---

## 5. Payroll Components & Engine

### 5.1 Tabel `payroll_periods`
- `id` BIGINT PK
- `company_id` BIGINT FK
- `code` VARCHAR(50) UNIQUE        -- contoh: 2025-01
- `name` VARCHAR(100) NULL         -- contoh: Januari 2025
- `start_date` DATE NOT NULL
- `end_date` DATE NOT NULL
- `status` ENUM('draft','calculated','approved','closed') DEFAULT 'draft'
- `locked_at` DATETIME NULL
- `created_at`, `updated_at`

---

### 5.2 Tabel `payroll_components`
- `id` BIGINT PK
- `company_id` BIGINT FK
- `name` VARCHAR(100) NOT NULL
- `code` VARCHAR(50) UNIQUE NOT NULL     -- contoh: BASIC_SALARY, BPJS_EMP, OVERTIME
- `type` ENUM('earning','deduction') NOT NULL
- `category` ENUM('fixed','variable','kpi','bpjs','tax','loan','other') NOT NULL
- `calculation_method` ENUM('manual','formula','attendance_based','kpi_based') DEFAULT 'manual'
- `is_taxable` TINYINT(1) DEFAULT 1
- `show_in_payslip` TINYINT(1) DEFAULT 1
- `sequence` INT DEFAULT 0               -- urutan tampil di slip
- `formula` TEXT NULL                    -- bisa pakai expression (opsional)
- `created_at`, `updated_at`

---

### 5.3 Tabel `employee_payroll_components`
Mapping komponen tetap ke karyawan.

- `id` BIGINT PK
- `employee_id` BIGINT FK
- `payroll_component_id` BIGINT FK
- `amount` DECIMAL(15,2) NOT NULL
- `effective_from` DATE NOT NULL
- `effective_to` DATE NULL
- `created_at`, `updated_at`

---

### 5.4 Tabel `employee_loans`
- `id` BIGINT PK
- `employee_id` BIGINT FK
- `company_id` BIGINT FK
- `loan_number` VARCHAR(50) UNIQUE
- `principal_amount` DECIMAL(15,2) NOT NULL
- `remaining_amount` DECIMAL(15,2) NOT NULL
- `installment_amount` DECIMAL(15,2) NOT NULL
- `start_period_id` BIGINT FK -> `payroll_periods.id`
- `end_period_id` BIGINT FK -> `payroll_periods.id` NULL
- `status` ENUM('active','completed','cancelled') DEFAULT 'active'
- `notes` TEXT NULL
- `created_at`, `updated_at`

---

### 5.5 Tabel `employee_loan_schedules`
- `id` BIGINT PK
- `employee_loan_id` BIGINT FK
- `payroll_period_id` BIGINT FK
- `amount` DECIMAL(15,2) NOT NULL
- `is_paid` TINYINT(1) DEFAULT 0
- `paid_at` DATETIME NULL
- `created_at`, `updated_at`

---

### 5.6 Tabel `payroll_headers`
Header payroll per karyawan per periode.

- `id` BIGINT PK
- `payroll_period_id` BIGINT FK
- `employee_id` BIGINT FK
- `gross_income` DECIMAL(15,2) DEFAULT 0
- `total_deduction` DECIMAL(15,2) DEFAULT 0
- `net_income` DECIMAL(15,2) DEFAULT 0
- `status` ENUM('draft','calculated','approved') DEFAULT 'draft'
- `generated_at` DATETIME NULL
- `approved_at` DATETIME NULL
- `approver_id` BIGINT FK -> `employees.id` NULL
- `created_at`, `updated_at`

Index:
- `idx_payroll_headers_period_emp` (payroll_period_id, employee_id)

---

### 5.7 Tabel `payroll_details`
Detail komponen per karyawan per periode.

- `id` BIGINT PK
- `payroll_header_id` BIGINT FK
- `payroll_component_id` BIGINT FK
- `amount` DECIMAL(15,2) NOT NULL
- `quantity` DECIMAL(10,2) NULL         -- misal jam lembur, hari, dsb.
- `remark` VARCHAR(255) NULL
- `created_at`, `updated_at`

---

## 6. Pajak PPh21

### 6.1 Tabel `tax_rates`
- `id` BIGINT PK
- `company_id` BIGINT FK
- `year` INT NOT NULL
- `range_min` DECIMAL(15,2) NOT NULL
- `range_max` DECIMAL(15,2) NULL
- `rate_percent` DECIMAL(5,2) NOT NULL
- `created_at`, `updated_at`

---

### 6.2 Tabel `tax_calculations`
Opsional jika ingin log detail.

- `id` BIGINT PK
- `payroll_header_id` BIGINT FK
- `annual_gross` DECIMAL(15,2) NOT NULL
- `deductible` DECIMAL(15,2) NOT NULL
- `pkp` DECIMAL(15,2) NOT NULL
- `annual_tax` DECIMAL(15,2) NOT NULL
- `monthly_tax` DECIMAL(15,2) NOT NULL
- `created_at`, `updated_at`

---

## 7. KPI & Performance-Based Pay (Detail Teknis)

### 7.1 Tabel `kpi_master`
- `id` BIGINT PK
- `company_id` BIGINT FK
- `name` VARCHAR(191) NOT NULL
- `code` VARCHAR(50) UNIQUE NOT NULL
- `type` ENUM('numeric','percent','boolean') NOT NULL
- `target_default` DECIMAL(15,2) NULL
- `weight_default` DECIMAL(5,2) NULL
- `period_type` ENUM('monthly','weekly','quarterly','yearly') NOT NULL
- `category` ENUM('individual','team','division') NOT NULL
- `description` TEXT NULL
- `created_at`, `updated_at`

---

### 7.2 Tabel `employee_kpi_assignments`
- `id` BIGINT PK
- `employee_id` BIGINT FK
- `kpi_id` BIGINT FK
- `target` DECIMAL(15,2) NULL
- `weight` DECIMAL(5,2) NULL
- `start_date` DATE NOT NULL
- `end_date` DATE NULL
- `created_at`, `updated_at`

Index:
- `idx_kpi_assign_emp_kpi` (employee_id, kpi_id)

---

### 7.3 Tabel `employee_kpi_results`
- `id` BIGINT PK
- `employee_id` BIGINT FK
- `kpi_id` BIGINT FK
- `period_start` DATE NOT NULL     -- contoh: 2025-01-01
- `period_end` DATE NOT NULL       -- contoh: 2025-01-31
- `actual_value` DECIMAL(15,2) NOT NULL
- `achievement_percentage` DECIMAL(5,2) NOT NULL
- `score` DECIMAL(5,2) NULL
- `evaluator_id` BIGINT FK -> `employees.id` NULL
- `notes` TEXT NULL
- `created_at`, `updated_at`

Index:
- `idx_kpi_results_emp_period` (employee_id, period_start, period_end)

---

### 7.4 Tabel `kpi_payroll_mapping`
Menghubungkan KPI dengan komponen payroll.

- `id` BIGINT PK
- `kpi_id` BIGINT FK
- `payroll_component_id` BIGINT FK
- `formula` TEXT NULL           -- misal: komisi = actual_value * rate
- `max_amount` DECIMAL(15,2) NULL
- `created_at`, `updated_at`

---

## 8. High-Level API Design (Hint)

Contoh struktur endpoint (REST):

### 8.1 Master Data
- `GET /api/employees`
- `POST /api/employees`
- `GET /api/employees/{id}`
- `PUT /api/employees/{id}`
- `DELETE /api/employees/{id}`

### 8.2 Absensi
- `POST /api/attendance/logs/import`
- `GET /api/attendance/summaries?period=2025-01&employee_id=...`
- `POST /api/attendance/manual-checkin`
- `POST /api/attendance/manual-checkout`

### 8.3 Cuti & Lembur
- `POST /api/leaves`
- `PUT /api/leaves/{id}/approve`
- `POST /api/overtimes`
- `PUT /api/overtimes/{id}/approve`

### 8.4 Payroll
- `POST /api/payroll-periods`                         -- create periode
- `POST /api/payroll-periods/{id}/generate`           -- generate payroll
- `PUT /api/payroll-periods/{id}/approve`
- `GET /api/payroll-periods/{id}/employees/{empId}`   -- detail slip

### 8.5 KPI
- `GET /api/kpi`
- `POST /api/kpi`
- `POST /api/kpi/assign`
- `POST /api/kpi/results/import`
- `GET /api/kpi/results?employee_id=...&period=...`

---

## 9. Monthly Payroll Algorithm (Pseudo Flow)

1. Pilih `payroll_period` (start_date, end_date).
2. Ambil semua karyawan `active`.
3. Untuk tiap karyawan:
   - Rekap absensi dari `attendance_summaries` dalam periode.
   - Hitung upah dasar + tunjangan tetap (`employee_payroll_components`).
   - Hitung variabel: lembur, uang makan, dll (berdasarkan absensi/lembur).
   - Hitung BPJS dengan `bpjs_rates` aktif.
   - Hitung PPh21 dengan `tax_rates` + `employee_tax_profiles`.
   - Hitung insentif KPI dari `employee_kpi_results` + `kpi_payroll_mapping`.
   - Terapkan potongan pinjaman (`employee_loan_schedules`).
   - Simpan ke `payroll_headers` & `payroll_details`.
4. Update `status` payroll_period ke `calculated`.
5. Setelah approval -> `approved`/`closed`.
6. Generate slip gaji (PDF) berdasarkan `payroll_headers` + `payroll_details`.

---

## 10. Catatan Implementasi

- Bisa diimplementasi dengan Laravel (migrations, models, policies).
- Pakai **soft delete** untuk tabel sensitif (`employees`, dsb.).
- Gunakan **transactions** saat generate payroll (supaya konsisten).
- Pertimbangkan **job queue** untuk proses berat (generate ribuan karyawan).
- Pisahkan modul per bounded context (HR, Absensi, Payroll, KPI).

---

## BAGIAN C - MODE FUNDRAISER / RELAWAN (CROWDFUNDING / LAZ / NGO)

# Fundraiser Payroll & Performance System - Blueprint (Crowdfunding / NGO / LAZ)

> Versi khusus untuk lembaga zakat / infaq / wakaf / crowdfunding,  
> di mana **"karyawan" adalah relawan / fundraiser** dengan pendapatan berbasis **jam kerja & perolehan dana**.

---

## 1. Project Overview

Sistem ini mengelola:

- Data relawan / fundraiser
- Absensi & jam kerja
- Pencatatan donasi yang dikumpulkan per relawan
- Perhitungan pendapatan berbasis:
  - Jam kerja (hourly)
  - Insentif / komisi dari perolehan dana
  - Bonus target & KPI
- Laporan perolehan dana & performa relawan

Tidak semua fitur payroll perusahaan formal wajib dipakai (misalnya BPJS & PPh21 bisa dinonaktifkan atau opsional).

---

## 2. Konsep Peran & Model Pembayaran

### 2.1 Role: Relawan / Fundraiser

Di dalam sistem, relawan tetap disimpan di tabel `employees`, tetapi:

- `is_volunteer = 1`
- Tidak wajib punya:
  - gaji pokok
  - tunjangan tetap
- Punya:
  - `hourly_rate`
  - `commission_rate` atau skema insentif berbasis tier

### 2.2 Model Pendapatan Relawan

1. **Pendapatan Jam Kerja (Hourly Income)**
   - Diambil dari absensi (check-in/out)
   - `total_hours = sum(worked_minutes) / 60`
   - `hourly_income = total_hours * hourly_rate`

2. **Insentif Perolehan Dana**
   - Berdasarkan total donasi yang dikumpulkan oleh relawan dalam periode
   - Bisa:
     - Persentase tetap (`commission_rate`)
     - Tier pencapaian (semakin tinggi perolehan, semakin besar persen insentif)

3. **Bonus KPI / Target**
   - Bonus ketika mencapai:
     - Target donasi (nominal)
     - Target donatur baru (jumlah)
     - Target jam aktif
   - Dipetakan sebagai komponen payroll kategori `kpi`

---

## 3. Perubahan / Penambahan Struktur Data

### 3.1 Tabel `employees` (Extend untuk Relawan)

Tambahan field:

- `is_volunteer` TINYINT(1) DEFAULT 0
- `fundraiser_type` ENUM('volunteer','freelance','field_agent','community_leader') DEFAULT 'volunteer'
- `hourly_rate` DECIMAL(15,2) DEFAULT 0
- `commission_rate` DECIMAL(5,2) DEFAULT 0      -- persen dari perolehan (opsional)
- `max_commission_cap` DECIMAL(15,2) NULL       -- batas maksimum insentif (opsional)

> Catatan: `basic_salary` boleh 0 untuk relawan.

---

### 3.2 Tabel `fundraising_transactions`

Mencatat setiap donasi yang **diasosiasikan dengan relawan tertentu** (fundraiser).

- `id` BIGINT PK
- `company_id` BIGINT FK -> `companies.id`
- `fundraiser_id` BIGINT FK -> `employees.id` (relawan)
- `donation_code` VARCHAR(50) UNIQUE NULL
- `donor_name` VARCHAR(191) NULL
- `donor_phone` VARCHAR(50) NULL
- `donor_email` VARCHAR(191) NULL
- `amount` DECIMAL(15,2) NOT NULL
- `currency` VARCHAR(10) DEFAULT 'IDR'
- `source` ENUM('offline','online','event','qr','transfer','other') NOT NULL
- `campaign_name` VARCHAR(191) NULL              -- nama program (Ramadhan, Qurban, dsb.)
- `category` ENUM('zakat','infaq','shodaqoh','wakaf','donation','other') NULL
- `date_received` DATETIME NOT NULL
- `notes` TEXT NULL
- `created_at`, `updated_at`

Index:
- `idx_fundraising_tx_fundraiser_date` (fundraiser_id, date_received)
- `idx_fundraising_tx_campaign` (campaign_name)

---

### 3.3 Tabel `fundraising_daily_summaries`

Rekap harian per relawan.

- `id` BIGINT PK
- `company_id` BIGINT FK
- `fundraiser_id` BIGINT FK -> `employees.id`
- `summary_date` DATE NOT NULL
- `total_amount` DECIMAL(15,2) NOT NULL          -- total donasi hari itu
- `total_transactions` INT NOT NULL
- `new_donors` INT NOT NULL                      -- donatur baru (opsional)
- `repeat_donors` INT NOT NULL                   -- donatur yang pernah nyumbang sebelumnya (opsional)
- `created_at`, `updated_at`

Index:
- `idx_fundraising_daily_fundraiser_date` (fundraiser_id, summary_date)

> Data ini bisa di-generate dari `fundraising_transactions` secara rutin (cron job).

---

## 4. KPI & Target Khusus Fundraiser

Modul KPI umum tetap dipakai (lihat blueprint teknis), kita tambahkan **preset KPI**:

### 4.1 Contoh KPI yang Umum

1. `TOTAL_DONATION_AMOUNT`
   - Target: nominal per periode
   - Tipe: numeric
2. `NEW_DONORS_COUNT`
   - Target: jumlah donatur baru
   - Tipe: numeric
3. `ACTIVE_HOURS`
   - Target: minimal jam aktif per periode (diambil dari absensi)
   - Tipe: numeric
4. `CONVERSION_RATE`
   - Target: persentase keberhasilan (opsional jika ada data prospek)
5. `EVENT_PARTICIPATION`
   - Target: minimal jumlah event yang diikuti

Semua KPI ini di-manage di:
- `kpi_master`
- `employee_kpi_assignments`
- `employee_kpi_results`

---

## 5. Payroll Components untuk Relawan

Di `payroll_components`, tambahkan beberapa komponen khusus:

### 5.1 Contoh Data `payroll_components`

1. **HOURLY_INCOME**
   - `type`: `earning`
   - `category`: `variable`
   - `calculation_method`: `attendance_based`
   - `formula`: bisa dikosongkan (hitung di kode)

2. **FUNDRAISING_COMMISSION**
   - `type`: `earning`
   - `category`: `kpi` atau `other`
   - `calculation_method`: `kpi_based` atau custom
   - `formula`: opsional (misal expression seperti `"total_donation * 0.03"`)

3. **TARGET_BONUS**
   - `type`: `earning`
   - `category`: `kpi`
   - `calculation_method`: `kpi_based`

4. (Opsional) **PENALTY_ABSENCE**
   - `type`: `deduction`
   - `category`: `other`
   - `calculation_method`: `attendance_based`

> BPJS, pajak, dan pinjaman bisa **tidak diaktifkan** untuk relawan jika secara hukum dianggap bukan pegawai tetap.

---

## 6. Algoritma Payroll Periode - Mode Fundraiser

Pseudo flow untuk relawan:

### 6.1 Step 1 - Ambil Data Absensi & Jam Kerja

Untuk setiap relawan (employee dengan `is_volunteer = 1`):

```pseudo
attendance_rows = attendance_summaries
    .where(employee_id = relawan.id)
    .where(work_date between period.start_date and period.end_date)

total_worked_minutes = sum(attendance_rows.worked_minutes)
total_hours = total_worked_minutes / 60
hourly_income = total_hours * relawan.hourly_rate
```

Simpan pendapatan ini sebagai 1 baris di `payroll_details` dengan komponen `HOURLY_INCOME`.

---

### 6.2 Step 2 - Ambil Total Perolehan Dana

```pseudo
fund_rows = fundraising_transactions
    .where(fundraiser_id = relawan.id)
    .where(date_received between period.start_date and period.end_date)

total_donation = sum(fund_rows.amount)
total_transactions = count(fund_rows)
```

---

### 6.3 Step 3 - Hitung Insentif Donasi (Komisi)

**Opsi A - Flat commission_rate dari employee:**

```pseudo
commission_amount = total_donation * (relawan.commission_rate / 100)

if relawan.max_commission_cap is not null and commission_amount > relawan.max_commission_cap:
    commission_amount = relawan.max_commission_cap
```

**Opsi B - Tiered commission (hardcoded atau di tabel konfigurasi lain):**

```pseudo
if total_donation >= 50_000_000:
    commission_rate = 7
elif total_donation >= 10_000_000:
    commission_rate = 4
elif total_donation >= 1_000_000:
    commission_rate = 2
else:
    commission_rate = 0

commission_amount = total_donation * commission_rate / 100
```

Simpan sebagai `payroll_details` komponen `FUNDRAISING_COMMISSION`.

---

### 6.4 Step 4 - Hitung Bonus KPI

Misal KPI: `NEW_DONORS_COUNT` & `TOTAL_DONATION_AMOUNT`

```pseudo
kpi_results = employee_kpi_results
    .where(employee_id = relawan.id)
    .where(period_start >= period.start_date)
    .where(period_end <= period.end_date)

for each kpi_result in kpi_results:
    if kpi_result.achievement_percentage >= 100:
        bonus += some_configured_bonus_value
```

Simpan sebagai `payroll_details` komponen `TARGET_BONUS`.

---

### 6.5 Step 5 - Hitung Total Pendapatan Relawan

```pseudo
gross_income = hourly_income + commission_amount + kpi_bonus
total_deduction = 0  -- jika tidak ada potongan
net_income = gross_income - total_deduction
```

Disimpan di `payroll_headers` untuk relawan tersebut.

---

## 7. Slip "Gaji" Relawan

Slip gaji untuk relawan bisa ditampilkan sebagai:

- Nama relawan
- Periode
- Ringkasan:
  - Total jam aktif & hourly income
  - Total perolehan dana & komisi
  - Bonus target/KPI
  - Pendapatan bersih yang diterima (jika ada real payout)
- Info tambahan:
  - Program / campaign yang paling banyak menghasilkan
  - Ranking fundraising (opsional)

---

## 8. Dashboard Khusus Fundraiser

### 8.1 Untuk Admin / Manajemen

- Tabel top fundraiser:
  - Nama
  - Total donasi bulan ini
  - Jam aktif
  - Komisi / insentif
- Grafik perolehan dana harian / mingguan / bulanan
- Filter per campaign (contoh: Ramadhan 1446H)

### 8.2 Untuk Relawan (Self-Service)

- Akumulasi donasi yang sudah berhasil dikumpulkan
- Estimasi insentif / komisi berjalan
- Jam aktif vs target jam
- Target & KPI dan status pencapaiannya

---

## 9. Integrasi dengan Blueprint Teknis Utama

Blueprint ini **tidak menggantikan**, tapi **menambahkan fokus baru**:

- Tetap gunakan:
  - `employees`, `attendance_summaries`, `payroll_periods`, `payroll_headers`, `payroll_details`, `kpi_master`, dll.
- Tambahan kunci:
  - Flag `is_volunteer`
  - Tabel `fundraising_transactions`
  - Tabel `fundraising_daily_summaries`
  - Komponen payroll khusus: `HOURLY_INCOME`, `FUNDRAISING_COMMISSION`, `TARGET_BONUS`

Dengan begitu:
- Satu sistem bisa melayani:
  - Pegawai tetap biasa (pakai gaji pokok, BPJS, pajak)
  - Relawan fundraiser (pakai jam kerja + insentif perolehan dana)

---

## 10. Catatan Implementasi Praktis

- Jika lembaga ingin mematuhi regulasi pajak:
  - Pembayaran rutin besar untuk fundraiser bisa dikenakan PPh (atur di komponen pajak).
- Jika ingin tetap disebut "insentif" bukan "gaji":
  - Di slip dan UI gunakan istilah: **"Laporan Insentif Relawan"**, bukan "Slip Gaji".
- Audit:
  - Penting untuk mengunci data fundraising per periode payroll agar nominal tidak berubah setelah perhitungan.

---

## 11. Next Step Otomatis yang Bisa Dibuat AI Agent

Dari blueprint ini, AI agent bisa:

1. Generate **Laravel migrations** untuk:
   - `fundraising_transactions`
   - `fundraising_daily_summaries`
   - field tambahan di `employees`
2. Generate **service class**:
   - `FundraisingSummaryService`
   - `VolunteerPayrollService`
3. Generate **API endpoint**:
   - `POST /api/fundraising/transactions`
   - `GET /api/fundraising/summary?period=...`
   - `POST /api/payroll-periods/{id}/generate-volunteer-payroll`
4. Generate **Blade/React/Vue components**:
   - Dashboard fundraiser
   - Slip insentif relawan

---

## BAGIAN D - GUIDELINE IMPLEMENTASI (URUT 1-3)

### 1) Migrations & Seed (Laravel contoh)

- **Alter `employees`**: tambah kolom `is_volunteer` (tinyint default 0), `fundraiser_type` (enum/ varchar: volunteer, freelance, field_agent, community_leader), `hourly_rate` (decimal 15,2 default 0), `commission_rate` (decimal 5,2 default 0), `max_commission_cap` (decimal 15,2 null). Index: pertahankan `idx_employees_company`.
- **Table `fundraising_transactions`**: sesuai skema di blueprint (PK id big, FK company_id, fundraiser_id ke employees, donation_code unique, donor_name/phone/email, amount decimal(15,2), currency default IDR, source enum, campaign_name, category, date_received datetime, notes, timestamps). Index: `(fundraiser_id, date_received)`, `(campaign_name)`.
- **Table `fundraising_daily_summaries`**: PK id big, company_id FK, fundraiser_id FK, summary_date date, total_amount decimal(15,2), total_transactions int, new_donors int, repeat_donors int, timestamps. Index: `(fundraiser_id, summary_date)`.
- **Seed `payroll_components` minimal** (per company):
  - `HOURLY_INCOME`: earning, category=variable, calculation_method=attendance_based, taxable? tergantung kebijakan.
  - `FUNDRAISING_COMMISSION`: earning, category=kpi/other, calculation_method=kpi_based/custom.
  - `TARGET_BONUS`: earning, category=kpi, calculation_method=kpi_based.
  - (Opsional) `PENALTY_ABSENCE`: deduction, category=other, calculation_method=attendance_based.
- **Naming migration contoh**:
  - `2025_02_01_000001_add_volunteer_fields_to_employees.php`
  - `2025_02_01_000002_create_fundraising_transactions_table.php`
  - `2025_02_01_000003_create_fundraising_daily_summaries_table.php`
  - `2025_02_01_000004_seed_volunteer_payroll_components.php`

---

### 2) Service / Job Flow

- **FundraisingSummaryService (job/command harian)**:
  - Input: tanggal (default kemarin) atau range.
  - Langkah: ambil `fundraising_transactions` per fundraiser, group by date; hitung total_amount, total_transactions, new_donors/repeat_donors (jika data tersedia); upsert ke `fundraising_daily_summaries`.
  - Locking: jalankan dalam transaksi per fundraiser/date; gunakan upsert agar idempotent.
- **VolunteerPayrollService (job per payroll_period)**:
  - Ambil volunteer (`employees.is_volunteer=1`) aktif dalam periode.
  - Hitung jam kerja dari `attendance_summaries` (worked_minutes) -> `hourly_income` (komponen HOURLY_INCOME).
  - Hitung total donasi dari `fundraising_transactions` dalam periode -> `commission_amount` pakai `commission_rate` dan `max_commission_cap` (komponen FUNDRAISING_COMMISSION).
  - Ambil KPI bonus dari `employee_kpi_results` + `kpi_payroll_mapping` (komponen TARGET_BONUS).
  - Susun `payroll_headers` (gross, deductions, net) + `payroll_details`.
  - Tandai `attendance_summaries.locked=1` dan kaitkan dengan `payroll_period` bila diperlukan untuk konsistensi.
- **Pertimbangan teknis**:
  - Jalankan di queue untuk skala besar.
  - Pakai transaksi per employee saat insert/update payroll_headers+details.
  - Catat log/ audit trail untuk nilai donasi yang dipakai (period start_end).

---

### 3) Endpoint API Dasar

- `POST /api/fundraising/transactions`  
  Payload: fundraiser_id, amount, source, campaign_name, category, date_received, donor info. Validasi fundraiser active & is_volunteer.
- `GET /api/fundraising/summary?period=YYYY-MM`  
  Output: total_amount, total_transactions, per-fundraiser breakdown; bisa ambil dari `fundraising_daily_summaries` agregat.
- `POST /api/payroll-periods/{id}/generate-volunteer-payroll`  
  Trigger VolunteerPayrollService untuk periode; response job id / status.
- `GET /api/payroll-periods/{id}/employees/{fundraiserId}`  
  Detail slip insentif relawan (komponen HOURLY_INCOME, FUNDRAISING_COMMISSION, TARGET_BONUS).
- (Opsional) `GET /api/fundraising/transactions?fundraiser_id=&date_between=` untuk audit.

---

### Checklist cepat sebelum implementasi
- Tentukan stack (Laravel/Node/Go/Python) dan tipe enum (pakai enum DB atau varchar + constraint).
- Siapkan seeder per company untuk komponen payroll khusus relawan.
- Pastikan timezone `Asia/Jakarta` dipakai saat agregasi tanggal/datetime.
- Lock data absensi dan fundraising saat payroll dihitung agar idempotent.

---

## Lampiran: Catatan Implementasi Laravel (singkat)

- Perintah awal:
  - `php artisan make:migration add_volunteer_fields_to_employees`
  - `php artisan make:migration create_fundraising_transactions_table`
  - `php artisan make:migration create_fundraising_daily_summaries_table`
  - `php artisan make:seeder VolunteerPayrollComponentsSeeder`
- Skema kolom (contoh ringkas, gunakan `enum` DB bila tersedia atau `string` + check constraint):
  - `is_volunteer` tinyInteger()->default(0)
  - `fundraiser_type` string(50)->default('volunteer')
  - `hourly_rate` decimal(15,2)->default(0)
  - `commission_rate` decimal(5,2)->default(0)
  - `max_commission_cap` decimal(15,2)->nullable()
  - FK `fundraiser_id` mereferensikan `employees.id`
- Seeder contoh (per company):
  ```php
  DB::table('payroll_components')->insert([
    ['company_id'=>1,'name'=>'Hourly Income','code'=>'HOURLY_INCOME','type'=>'earning','category'=>'variable','calculation_method'=>'attendance_based','is_taxable'=>0,'show_in_payslip'=>1,'sequence'=>10],
    ['company_id'=>1,'name'=>'Fundraising Commission','code'=>'FUNDRAISING_COMMISSION','type'=>'earning','category'=>'kpi','calculation_method'=>'kpi_based','is_taxable'=>0,'show_in_payslip'=>1,'sequence'=>20],
    ['company_id'=>1,'name'=>'Target Bonus','code'=>'TARGET_BONUS','type'=>'earning','category'=>'kpi','calculation_method'=>'kpi_based','is_taxable'=>0,'show_in_payslip'=>1,'sequence'=>30],
  ]);
  ```
- Service/Job:
  - `FundraisingSummaryService`: gunakan query builder aggregate, `upsert` ke `fundraising_daily_summaries`.
  - `VolunteerPayrollService`: jalankan dalam transaction per karyawan; buat/ perbarui `payroll_headers` dan `payroll_details`; set `attendance_summaries.locked=1` setelah tersimpan.
- Konfigurasi:
  - Set `APP_TIMEZONE=Asia/Jakarta`.
  - Pastikan queue driver aktif (redis/database) untuk job generate payroll.
  - Tambahkan policy/guard agar hanya relawan (`is_volunteer=1`) yang bisa dicatat di endpoint fundraising.

