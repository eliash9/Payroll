<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisbursementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'disbursement_id',
        'item_description',
        'quantity',
        'unit_value',
        'total_value',
    ];

    protected $casts = [
        'unit_value' => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    public function disbursement()
    {
        return $this->belongsTo(Disbursement::class);
    }
}
