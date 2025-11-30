<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LazInitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get a default company ID (assuming at least one company exists)
        $companyId = DB::table('companies')->value('id');
        
        if (!$companyId) {
            // Create a default company if none exists
            $companyId = DB::table('companies')->insertGetId([
                'name' => 'Default Company',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ensure Branches exist
        $branches = [
            ['name' => 'Pusat', 'code' => 'PST', 'address' => 'Kantor pusat Sidogiri'],
            ['name' => 'Surabaya', 'code' => 'SBY', 'address' => 'Jl. Contoh Surabaya'],
            ['name' => 'Pasuruan', 'code' => 'PSN', 'address' => 'Jl. Contoh Pasuruan'],
        ];
        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['code' => $branch['code']], 
                array_merge($branch, ['company_id' => $companyId])
            );
        }

        // Seed LAZ Roles
        $roles = ['super_admin', 'admin_pusat', 'admin_cabang', 'surveyor', 'approver', 'keuangan', 'auditor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
