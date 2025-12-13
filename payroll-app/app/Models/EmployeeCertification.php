<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCertification extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'issuer',
        'issue_date',
        'expiry_date',
        'credential_number',
        'url',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
