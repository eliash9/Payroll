<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft',
        'submitted',
        'screening',
        'survey_assigned',
        'surveying',
        'waiting_approval',
        'approved',
        'rejected',
        'disbursement_in_progress',
        'completed',
    ];

    protected $fillable = [
        'code',
        'program_id',
        'program_period_id',
        'applicant_type',
        'applicant_id',
        'organization_id',
        'branch_id',
        'requested_amount',
        'requested_aid_type',
        'need_description',
        'location_province',
        'location_regency',
        'location_district',
        'location_village',
        'status',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function period()
    {
        return $this->belongsTo(ProgramPeriod::class, 'program_period_id');
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function disbursements()
    {
        return $this->hasMany(Disbursement::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getApplicantNameAttribute(): string
    {
        if ($this->applicant_type === 'organization' && $this->organization) {
            return $this->organization->name;
        }

        return $this->applicant?->full_name ?? '-';
    }
}
