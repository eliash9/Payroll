<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'approver_id',
        'decided_at',
        'decision',
        'approved_amount',
        'approved_aid_type',
        'notes',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
        'approved_amount' => 'decimal:2',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
