<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_template_id',
        'question',
        'type',
        'options',
        'order',
        'is_required',
        'weight',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(SurveyTemplate::class, 'survey_template_id');
    }
}
