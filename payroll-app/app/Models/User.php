<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'company_id',
        'branch_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class, 'surveyor_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    public function disbursements()
    {
        return $this->hasMany(Disbursement::class, 'disbursed_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function hasRole(string|array $roles): bool
    {
        $target = \Illuminate\Support\Collection::wrap($roles);

        return $this->roles->pluck('name')->intersect($target)->isNotEmpty();
    }

    public function canAccessLaz(): bool
    {
        return $this->role === 'admin' || $this->roles()->exists();
    }
}
