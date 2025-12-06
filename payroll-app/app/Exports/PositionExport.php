<?php

namespace App\Exports;

use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PositionExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Position::query();
        
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
            'Code',
            'Name',
            'Description',
        ];
    }

    public function map($position): array
    {
        return [
            $position->id,
            $position->company_id,
            $position->code,
            $position->name,
            $position->description,
        ];
    }
}
