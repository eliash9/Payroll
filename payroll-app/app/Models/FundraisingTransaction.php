<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundraisingTransaction extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'date_received' => 'datetime',
        'amount' => 'decimal:2',
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
