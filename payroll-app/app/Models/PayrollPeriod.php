<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'start_date',
        'end_date',
        'status',
        'locked_at',
    ];
}
