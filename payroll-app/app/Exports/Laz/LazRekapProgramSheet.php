<?php

namespace App\Exports\Laz;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LazRekapProgramSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function styles(Worksheet $sheet)
    {
        $companyName = \App\Models\Company::first()->name ?? config('app.name');
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', $companyName . ' - Rekap Per Program');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        
        return [
            3 => ['font' => ['bold' => true]],
        ];
    }
    protected $perProgram;
    protected $approved;
    protected $disbursed;

    public function __construct($perProgram, $approved, $disbursed)
    {
        $this->perProgram = $perProgram;
        $this->approved = $approved;
        $this->disbursed = $disbursed;
    }

    public function collection()
    {
        return $this->perProgram->map(function ($row) {
            return [
                'program' => $row->program->name ?? '-',
                'submitted' => $row->total_submitted,
                'approved_count' => $row->total_approved,
                'rejected_count' => $row->total_rejected,
                'requested_amount' => $row->total_requested,
                'approved_amount' => $this->approved[$row->program_id] ?? 0,
                'disbursed_amount' => $this->disbursed[$row->program_id] ?? 0,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Program', 'Diajukan', 'Disetujui (Jml)', 'Ditolak (Jml)', 'Dana Diminta', 'Dana Disetujui', 'Dana Disalurkan'
        ];
    }

    public function title(): string
    {
        return 'Per Program';
    }
}
