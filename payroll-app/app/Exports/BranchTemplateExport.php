<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BranchTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                'Code' => 'CBG001',
                'Name' => 'Cabang Utama',
                'Address' => 'Jl. Sudirman No. 1',
                'Phone' => '021-1234567',
                'Latitude' => '-6.2088',
                'Longitude' => '106.8456',
                'Grade' => 'A',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Address',
            'Phone',
            'Latitude',
            'Longitude',
            'Grade',
        ];
    }
}
