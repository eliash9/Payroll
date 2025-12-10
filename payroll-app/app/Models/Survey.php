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
        'survey_template_id',
        'survey_date',
        'method',
        'summary',
        'economic_condition_score',
        'recommendation',
        'notes',
        'total_score',
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

    public function template()
    {
        return $this->belongsTo(SurveyTemplate::class, 'survey_template_id');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function photos()
    {
        return $this->hasMany(SurveyPhoto::class);
    }
}
