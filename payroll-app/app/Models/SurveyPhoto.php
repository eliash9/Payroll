<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'file_path',
        'caption',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
