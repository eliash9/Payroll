<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'allowed_recipient_type',
        'coverage_scope',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function periods(): HasMany
    {
        return $this->hasMany(ProgramPeriod::class);
    }

    public function activePeriods(): HasMany
    {
        return $this->periods()->where('status', 'open')
            ->whereRaw('? between open_at and close_at', [now()]);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
