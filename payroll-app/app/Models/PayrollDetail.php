<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $guarded = ['id'];

    public function header()
    {
        return $this->belongsTo(PayrollHeader::class, 'payroll_header_id');
    }

    public function component()
    {
        return $this->belongsTo(PayrollComponent::class, 'payroll_component_id');
    }
}
