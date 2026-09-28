<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'first_name',
        'last_name',
        'dob',
        'age',
        'gender',
        'blood_group',
        'phone',
        'email',
        'password',
        'address',
        'city',
        'state',
        'pin_code',
        'allergies',
        'existing_conditions',
        'current_medications',
        'previous_history',
        'family_history',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class)->latest('appointment_date');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class)->latest('consultation_date');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class)->latest('prescription_date');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class)->latest('invoice_date');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->latest('payment_date');
    }

    public function documents()
    {
        return $this->hasMany(PatientDocument::class)->latest();
    }

    public function notifications()
    {
        return $this->hasMany(PatientNotification::class)->latest();
    }

    public function lastVisit()
    {
        return $this->appointments()
            ->whereIn('status', ['Completed', 'Checked In', 'In Consultation'])
            ->first();
    }

    public function nextAppointment()
    {
        return $this->appointments()
            ->whereIn('status', ['Pending', 'Confirmed'])
            ->where('appointment_date', '>=', now()->format('Y-m-d'))
            ->first();
    }
}
