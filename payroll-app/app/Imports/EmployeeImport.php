<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class EmployeeImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $companyId = Auth::user()->company_id ?? $row['company_id'];
        
        // Basic validation/sanitization logic
        $isVolunteerRaw = $row['is_volunteer'] ?? '';
        $isVolunteer = strtolower((string)$isVolunteerRaw) === 'yes' || $isVolunteerRaw === '1' || $isVolunteerRaw === 1;

        return new Employee([
            'company_id' => $companyId,
            'employee_code' => $row['employee_code'],
            'full_name' => $row['full_name'],
            'nickname' => $row['nickname'] ?? null,
            'national_id_number' => $row['nik'] ?? null,
            'family_card_number' => $row['kk'] ?? null,
            'birth_place' => $row['birth_place'] ?? null,
            'birth_date' => $this->transformDate($row['birth_date']),
            'gender' => $row['gender'] ?? null,
            'marital_status' => $row['marital_status'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
            'branch_id' => $row['branch_id'] ?? null,
            'department_id' => $row['department_id'] ?? null,
            'position_id' => $row['position_id'] ?? null,
            'is_volunteer' => $isVolunteer,
            'basic_salary' => $row['basic_salary'] ?? 0,
            'hourly_rate' => $row['hourly_rate'] ?? 0,
            'commission_rate' => $row['commission_rate'] ?? 0,
            'employment_type' => $row['employment_type'] ?? 'permanent',
            'status' => $row['status'] ?? 'active',
            'join_date' => $this->transformDate($row['join_date']),
        ]);
    }

    public function rules(): array
    {
        return [
            'employee_code' => ['required', 'unique:employees,employee_code'],
            'full_name' => ['required'],
            'company_id' => ['nullable', 'integer'],
            'email' => ['nullable', 'email'],
            'employment_type' => ['nullable', 'in:permanent,contract,intern,outsourcing'],
            'status' => ['nullable', 'in:active,inactive,suspended,terminated'],
        ];
    }

    private function transformDate($value)
    {
        if (!$value) return null;
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
