<?php

namespace App\Exports;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BranchExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Branch::query();
        
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
            'Address',
            'Phone',
            'Latitude',
            'Longitude',
            'Grade',
        ];
    }

    public function map($branch): array
    {
        return [
            $branch->id,
            $branch->company_id,
            $branch->code,
            $branch->name,
            $branch->address,
            $branch->phone,
            $branch->latitude,
            $branch->longitude,
            $branch->grade,
        ];
    }
}
