<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'can_manage_employees',
        'can_manage_payouts',
        'can_manage_attendance',
        'can_view_reports',
        'can_view_employee_profiles',
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
            'is_admin' => 'boolean',
            'can_manage_employees' => 'boolean',
            'can_manage_payouts' => 'boolean',
            'can_manage_attendance' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_view_employee_profiles' => 'boolean',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return match ($permission) {
            'manage_employees' => (bool) $this->can_manage_employees,
            'manage_payouts' => (bool) $this->can_manage_payouts,
            'manage_attendance' => (bool) $this->can_manage_attendance,
            'view_reports' => (bool) $this->can_view_reports,
            'view_employee_profiles' => (bool) $this->can_view_employee_profiles,
            default => false,
        };
    }
}
