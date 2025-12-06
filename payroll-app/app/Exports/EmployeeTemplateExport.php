<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                'ID' => '',
                'Company ID' => '1',
                'Employee Code' => 'EMP001',
                'Full Name' => 'John Doe',
                'Nickname' => 'John',
                'NIK' => '1234567890123456',
                'KK' => '1234567890123456',
                'Birth Place' => 'Jakarta',
                'Birth Date' => '1990-01-01',
                'Gender' => 'male',
                'Marital Status' => 'single',
                'Email' => 'john@example.com',
                'Phone' => '08123456789',
                'Address' => 'Jl. Sudirman No. 1',
                'Branch ID' => '1',
                'Department ID' => '1',
                'Position ID' => '1',
                'Is Volunteer' => 'No',
                'Basic Salary' => '5000000',
                'Hourly Rate' => '0',
                'Commission Rate' => '0',
                'Employment Type' => 'permanent',
                'Status' => 'active',
                'Join Date' => '2023-01-01',
            ]
        ]);
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
}
