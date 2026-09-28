<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'patient_id',
        'appointment_id',
        'doctor_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status',
        'notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculateTotals()
    {
        $this->subtotal = $this->items()->sum('total');
        $this->total_amount = max(0, $this->subtotal - $this->discount + $this->tax);
        $this->paid_amount = $this->payments()->sum('amount');
        $this->due_amount = max(0, $this->total_amount - $this->paid_amount);

        if ($this->due_amount <= 0 && $this->total_amount > 0) {
            $this->status = 'Paid';
        } elseif ($this->paid_amount > 0 && $this->due_amount > 0) {
            $this->status = 'Partially Paid';
        } elseif ($this->status !== 'Cancelled' && $this->status !== 'Draft') {
            $this->status = 'Unpaid';
        }

        $this->save();

        if ($this->appointment) {
            $this->appointment->update(['payment_status' => $this->status]);
        }
    }
}
