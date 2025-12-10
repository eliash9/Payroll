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
        'is_headquarters',
        'code',
        'name',
        'address',
        'latitude',
        'longitude',
        'grade',
        'phone',
        'province_code',
        'province_name',
        'city_code',
        'city_name',
        'district_code',
        'district_name',
        'village_code',
        'village_name',
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
