<?php

namespace App\Exports\Laz;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LazDetailExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function styles(Worksheet $sheet)
    {
        $companyName = \App\Models\Company::first()->name ?? config('app.name');
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', $companyName . ' - Laporan Detail Permohonan LAZ');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        return [
            3 => ['font' => ['bold' => true]], // Header row is now at index 3
        ];
    }
    public function query()
    {
        return Application::query()
            ->with(['program', 'period', 'applicant', 'organization', 'branch']);
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Program',
            'Periode',
            'Tipe Pemohon',
            'Nama Pemohon',
            'Cabang',
            'Jumlah Diminta',
            'Jenis Bantuan',
            'Provinsi',
            'Kota/Kab',
            'Status',
            'Tanggal Pengajuan',
        ];
    }

    public function map($application): array
    {
        return [
            $application->code,
            $application->program->name ?? '-',
            $application->period->name ?? '-',
            $application->applicant_type,
            $application->applicant_name,
            $application->branch->name ?? '-',
            $application->requested_amount,
            $application->requested_aid_type,
            $application->location_province,
            $application->location_regency,
            $application->status,
            $application->created_at->format('Y-m-d H:i'),
        ];
    }
}
