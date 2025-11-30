<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'registration_number',
        'address',
        'contact_phone',
        'contact_email',
        'responsible_person',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
