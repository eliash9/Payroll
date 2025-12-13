<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $fillable = [
        'employee_id',
        'institution_name',
        'degree',
        'major',
        'start_year',
        'end_year',
        'gpa',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
