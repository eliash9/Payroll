<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobResponsibility extends Model
{
    protected $fillable = [
        'job_id',
        'responsibility',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
