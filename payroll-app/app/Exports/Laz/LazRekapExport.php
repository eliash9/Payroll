<?php

namespace App\Exports\Laz;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LazRekapExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new LazRekapProgramSheet($this->data['perProgram'], $this->data['perProgramApproved'], $this->data['perProgramDisbursed']),
            new LazRekapMonthSheet($this->data['perMonth']),
            new LazRekapApplicantSheet($this->data['segmentApplicant']),
            new LazRekapProvinceSheet($this->data['segmentProvince']),
        ];
    }
}
