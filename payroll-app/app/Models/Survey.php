<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'surveyor_id',
        'survey_date',
        'method',
        'summary',
        'economic_condition_score',
        'recommendation',
        'notes',
    ];

    protected $casts = [
        'survey_date' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    public function photos()
    {
        return $this->hasMany(SurveyPhoto::class);
    }
}
