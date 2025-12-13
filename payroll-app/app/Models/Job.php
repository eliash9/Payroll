<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCompanyScope;

class Job extends Model
{
    use SoftDeletes, HasCompanyScope;

    protected $table = 'job_profiles';

    protected $fillable = [
        'company_id',
        'title',
        'code',
        'description',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function responsibilities()
    {
        return $this->hasMany(JobResponsibility::class);
    }

    public function requirements()
    {
        return $this->hasMany(JobRequirement::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
