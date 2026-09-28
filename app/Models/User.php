<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'qualifications',
        'specialization',
        'registration_number',
        'experience_years',
        'consultation_fee',
        'cabin_number',
        'working_hours',
        'password',
        'role_id',
        'role_slug',
        'permissions',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'consultation_fee' => 'decimal:2',
            'permissions' => 'array',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    public function documents()
    {
        return $this->hasMany(StaffDocument::class)->latest();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role_slug === 'admin') {
            return true;
        }

        if (is_array($this->permissions)) {
            if (in_array('*', $this->permissions)) {
                return true;
            }
            if (in_array($permission, $this->permissions)) {
                return true;
            }
        }

        if ($this->role) {
            return $this->role->hasPermission($permission);
        }

        return false;
    }
}
