<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepartmentTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                'Code' => 'DEP001',
                'Name' => 'Keuangan',
                'Description' => 'Departemen Keuangan',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Description',
        ];
    }
}
