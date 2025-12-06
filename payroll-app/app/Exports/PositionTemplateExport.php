<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PositionTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                'Code' => 'POS001',
                'Name' => 'Manager',
                'Description' => 'Manager Level',
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
