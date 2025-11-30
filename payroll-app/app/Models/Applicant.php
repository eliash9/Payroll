<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id',
        'full_name',
        'birth_date',
        'address',
        'phone',
        'email',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
