<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
        'end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'max_commission_cap' => 'decimal:2',
        'is_volunteer' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fundraisingTransactions()
    {
        return $this->hasMany(FundraisingTransaction::class, 'fundraiser_id');
    }

    public function fundraisingDailySummaries()
    {
        return $this->hasMany(FundraisingDailySummary::class, 'fundraiser_id');
    }
}
