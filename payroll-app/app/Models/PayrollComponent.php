<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollComponent extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'type',
        'category',
        'calculation_method',
        'is_taxable',
        'show_in_payslip',
        'sequence',
        'formula',
    ];
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_payroll_components')
            ->withPivot('amount', 'effective_from', 'effective_to')
            ->withTimestamps();
    }
}
