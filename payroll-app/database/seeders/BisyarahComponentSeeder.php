<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class BisyarahComponentSeeder extends Seeder
{
    public function run()
    {
        $company = Company::first();
        if (!$company) {
            $this->command->info('No company found. Seeder skipped.');
            return;
        }

        $components = [
            // Earning
            [
                'code' => 'TUNJANGAN_JABATAN',
                'name' => 'Tunjangan Jabatan',
                'type' => 'earning',
                'category' => 'fixed',
                'calculation_method' => 'manual', 
                'is_taxable' => true,
                'note' => 'Flet, Perjabatan - Diinput di Master Karyawan'
            ],
            [
                'code' => 'PIKET',
                'name' => 'Uang Piket',
                'type' => 'earning',
                'category' => 'variable',
                'calculation_method' => 'formula',
                'formula' => 'amount * hours',
                'is_taxable' => true,
                'note' => 'Perjam, Perjabatan - Input Rate di Master Karyawan, Jam ambil dari Lembur/Absensi'
            ],
            [
                'code' => 'TRANSPORT',
                'name' => 'Tunjangan Transport',
                'type' => 'earning',
                'category' => 'variable',
                'calculation_method' => 'attendance_based',
                'formula' => null,
                'is_taxable' => true,
                'note' => 'Perhari - Input Rate Harian di Master Karyawan, dikali Kehadiran'
            ],
            [
                'code' => 'UANG_MAKAN',
                'name' => 'Uang Makan',
                'type' => 'earning',
                'category' => 'variable',
                'calculation_method' => 'attendance_based',
                'formula' => null,
                'is_taxable' => true,
                'note' => 'Perhari, Perkaryawan - Input Rate Harian di Master Karyawan'
            ],
            [
                'code' => 'BONUS_TRANSAKSI',
                'name' => 'Bonus Transaksi',
                'type' => 'earning',
                'category' => 'kpi',
                'calculation_method' => 'manual',
                'is_taxable' => true,
                'note' => 'Sesuai Perolehan'
            ],
             [
                'code' => 'BONUS_KINERJA',
                'name' => 'Bonus Kinerja',
                'type' => 'earning',
                'category' => 'kpi',
                'calculation_method' => 'manual',
                'is_taxable' => true,
                'note' => 'Sesuai Perolehan'
            ],
            [
                'code' => 'BONUS_PRESTASI',
                'name' => 'Bonus Prestasi',
                'type' => 'earning',
                'category' => 'kpi',
                'calculation_method' => 'manual',
                'is_taxable' => true,
                'note' => 'Sesuai Perolehan'
            ],

            // Deductions
            [
                'code' => 'DANSOS',
                'name' => 'Dansos Kemashlahatan',
                'type' => 'deduction',
                'category' => 'other',
                'calculation_method' => 'manual',
                'is_taxable' => false,
                'note' => 'Potongan sosial'
            ],
            [
                'code' => 'INFAK_LAZ',
                'name' => 'Infak LAZ',
                'type' => 'deduction',
                'category' => 'other',
                'calculation_method' => 'manual',
                'is_taxable' => false,
                'note' => 'Potongan infak'
            ],
            [
                'code' => 'WAKAF_LKAF',
                'name' => 'Wakaf L-KAF',
                'type' => 'deduction',
                'category' => 'other',
                'calculation_method' => 'manual',
                'is_taxable' => false,
                'note' => 'Potongan wakaf'
            ],
            [
                'code' => 'TABUNGAN_PROGRAM',
                'name' => 'Tabungan Program',
                'type' => 'deduction',
                'category' => 'other',
                'calculation_method' => 'manual',
                'is_taxable' => false,
                'note' => 'Tabungan wajib'
            ],
             [
                'code' => 'ABSENSI_DEDUCTION',
                'name' => 'Potongan Absensi',
                'type' => 'deduction',
                'category' => 'variable',
                'calculation_method' => 'manual', // Logic handled via Overtime/Regular service usually, or manual
                'is_taxable' => false,
                'note' => 'Potongan ketidakhadiran'
            ],
        ];

        foreach ($components as $comp) {
            // Remove 'note' before insert
            $note = $comp['note'];
            unset($comp['note']);
            
            DB::table('payroll_components')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'code' => $comp['code']
                ],
                array_merge($comp, [
                    'show_in_payslip' => true,
                    'sequence' => 10,
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            
            $this->command->info("Seeded: {$comp['name']} ({$note})");
        }
    }
}
