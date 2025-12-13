<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollHeader extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'generated_at' => 'datetime',
        'locked_at' => 'datetime',
        'gross_income' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_income' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
