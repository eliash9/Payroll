<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerHistory extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'effective_date' => 'date',
        'old_basic_salary' => 'decimal:2',
        'new_basic_salary' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Old Relationships
    public function oldBranch() { return $this->belongsTo(Branch::class, 'old_branch_id'); }
    public function oldDepartment() { return $this->belongsTo(Department::class, 'old_department_id'); }
    public function oldPosition() { return $this->belongsTo(Position::class, 'old_position_id'); }

    // New Relationships
    public function newBranch() { return $this->belongsTo(Branch::class, 'new_branch_id'); }
    public function newDepartment() { return $this->belongsTo(Department::class, 'new_department_id'); }
    public function newPosition() { return $this->belongsTo(Position::class, 'new_position_id'); }
}
