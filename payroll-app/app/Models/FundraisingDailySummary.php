<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundraisingDailySummary extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'summary_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function fundraiser()
    {
        return $this->belongsTo(Employee::class, 'fundraiser_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
