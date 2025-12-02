<?php

namespace App\Exports\Laz;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LazRekapApplicantSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function styles(Worksheet $sheet)
    {
        $companyName = \App\Models\Company::first()->name ?? config('app.name');
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', $companyName . ' - Segmentasi Pemohon');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        
        return [
            3 => ['font' => ['bold' => true]],
        ];
    }
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($row) {
            return [
                'type' => $row->applicant_type,
                'total' => $row->total,
                'requested' => $row->total_requested,
            ];
        });
    }

    public function headings(): array
    {
        return ['Tipe Pemohon', 'Jumlah', 'Dana Diminta'];
    }

    public function title(): string
    {
        return 'Segmentasi Pemohon';
    }
}
