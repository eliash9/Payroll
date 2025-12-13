<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicationBeneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'name',
        'national_id',
        'address',
        'phone',
        'description',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}

