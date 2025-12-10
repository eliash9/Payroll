<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLocation extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'latitude',
        'longitude',
        'radius',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_work_location');
    }
}
