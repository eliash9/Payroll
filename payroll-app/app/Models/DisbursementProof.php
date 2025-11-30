<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisbursementProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'disbursement_id',
        'file_path',
        'caption',
    ];

    public function disbursement()
    {
        return $this->belongsTo(Disbursement::class);
    }
}
