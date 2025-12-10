<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyTemplate extends Model
{
    protected $fillable = [
        'program_id',
        'code',
        'title',
        'description',
        'is_active',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }
}
