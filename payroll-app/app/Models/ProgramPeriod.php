<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'name',
        'open_at',
        'close_at',
        'application_quota',
        'budget_quota',
        'status',
    ];

    protected $casts = [
        'open_at' => 'datetime',
        'close_at' => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
            ->where('open_at', '<=', Carbon::now())
            ->where('close_at', '>=', Carbon::now());
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' 
            && $this->open_at <= Carbon::now() 
            && $this->close_at >= Carbon::now();
    }
}
