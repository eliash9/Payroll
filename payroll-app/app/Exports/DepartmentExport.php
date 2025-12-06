<?php

namespace App\Exports;

use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DepartmentExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Department::query();
        
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

    public function map($department): array
    {
        return [
            $department->id,
            $department->company_id,
            $department->code,
            $department->name,
            $department->description,
        ];
    }
}
