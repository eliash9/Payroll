<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCompanyScope;

class Branch extends Model
{
    use SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'address',
        'latitude',
        'longitude',
        'grade',
        'phone',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
