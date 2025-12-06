<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Employee::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company ID',
            'Employee Code',
            'Full Name',
            'Nickname',
            'NIK',
            'KK',
            'Birth Place',
            'Birth Date',
            'Gender',
            'Marital Status',
            'Email',
            'Phone',
            'Address',
            'Branch ID',
            'Department ID',
            'Position ID',
            'Is Volunteer',
            'Basic Salary',
            'Hourly Rate',
            'Commission Rate',
            'Employment Type',
            'Status',
            'Join Date',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->company_id,
            $employee->employee_code,
            $employee->full_name,
            $employee->nickname,
            $employee->national_id_number,
            $employee->family_card_number,
            $employee->birth_place,
            $employee->birth_date ? $employee->birth_date->format('Y-m-d') : null,
            $employee->gender,
            $employee->marital_status,
            $employee->email,
            $employee->phone,
            $employee->address,
            $employee->branch_id,
            $employee->department_id,
            $employee->position_id,
            $employee->is_volunteer ? 'Yes' : 'No',
            $employee->basic_salary,
            $employee->hourly_rate,
            $employee->commission_rate,
            $employee->employment_type,
            $employee->status,
            $employee->join_date ? $employee->join_date->format('Y-m-d') : null,
        ];
    }
}
