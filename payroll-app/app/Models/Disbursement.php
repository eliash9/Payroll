<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'disbursed_by',
        'disbursed_at',
        'method',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'disbursed_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function items()
    {
        return $this->hasMany(DisbursementItem::class);
    }

    public function proofs()
    {
        return $this->hasMany(DisbursementProof::class);
    }
}
