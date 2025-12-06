<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCompanyScope;

class Position extends Model
{
    use SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
