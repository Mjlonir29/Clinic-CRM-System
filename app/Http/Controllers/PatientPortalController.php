<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicNotification;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientPortalController extends Controller
{
    /**
     * Display the public customer / patient appointment booking portal.
     */
    public function showBooking()
    {
        // Get active doctors or fallback doctors
        $doctors = User::whereIn('role_slug', ['doctor', 'admin'])->get();

        if ($doctors->isEmpty()) {
            $doctors = User::all();
        }

        // Available time slots
        $timeSlots = [
            '09:00 AM',
            '09:30 AM',
            '10:00 AM',
            '10:30 AM',
            '11:15 AM',
            '11:45 AM',
            '02:00 PM',
            '02:30 PM',
            '03:15 PM',
            '04:00 PM',
            '04:45 PM',
            '05:30 PM',
        ];

        $today = Carbon::today()->format('Y-m-d');

        return view('patient_portal.book', compact('doctors', 'timeSlots', 'today'));
    }

    /**
     * Store a customer-booked appointment.
     */
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|string',
            'age' => 'nullable|integer|min:1|max:120',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'appointment_type' => 'required|string',
            'reason_for_visit' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        // Search for existing patient by phone or email
        $patient = Patient::where('phone', $validated['phone'])->first();

        if (!$patient && !empty($validated['email'])) {
            $patient = Patient::where('email', $validated['email'])->first();
        }

        // If patient does not exist, register them in database
        if (!$patient) {
            $latestPatient = Patient::latest('id')->first();
            $nextPatientNum = $latestPatient ? $latestPatient->id + 1 : 1;
            $patientId = 'PAT-' . date('Y') . '-' . str_pad($nextPatientNum, 4, '0', STR_PAD_LEFT);

            $patient = Patient::create([
                'patient_id' => $patientId,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'gender' => $validated['gender'],
                'age' => $validated['age'] ?? 30,
                'allergies' => $validated['allergies'] ?? null,
                'status' => 'Active',
            ]);
        }

        // Generate Appointment Number
        $latestApt = Appointment::latest('id')->first();
        $nextAptNum = $latestApt ? $latestApt->id + 1 : 1;
        $appointmentNumber = 'APT-' . date('Y') . '-' . str_pad($nextAptNum, 4, '0', STR_PAD_LEFT);

        // Assign Doctor
        $doctorId = $validated['doctor_id'];
        if (empty($doctorId)) {
            $defaultDoctor = User::whereIn('role_slug', ['doctor', 'admin'])->first();
            $doctorId = $defaultDoctor ? $defaultDoctor->id : 1;
        }

        // Create Appointment record
        $appointment = Appointment::create([
            'appointment_number' => $appointmentNumber,
            'patient_id' => $patient->id,
            'doctor_id' => $doctorId,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'appointment_type' => $validated['appointment_type'],
            'reason_for_visit' => $validated['reason_for_visit'],
            'notes' => 'Online Customer Booking via Hospital Portal',
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
        ]);

        // Send real-time notification to doctor/admin
        $doctorUser = User::find($doctorId);
        ClinicNotification::createNotification(
            $doctorId,
            'online_booking',
            '⚡ New Customer Online Booking',
            "Patient {$patient->full_name} booked {$appointment->appointment_type} for {$appointment->appointment_date} at {$appointment->appointment_time}.",
            "/appointments"
        );

        // Send real-time notification to patient portal account
        \App\Models\PatientNotification::createNotification(
            $patient->id,
            'appointment_reminder',
            'Appointment Booked Successfully',
            "Your appointment (#{$appointment->appointment_number}) for {$appointment->appointment_type} on {$appointment->appointment_date} at {$appointment->appointment_time} has been registered.",
            "#appointments-section"
        );

        $assignedDoctorName = $doctorUser ? $doctorUser->name : 'Apex Duty Specialist';

        return back()->with('booking_success', [
            'appointment_number' => $appointment->appointment_number,
            'patient_name' => $patient->full_name,
            'doctor_name' => $assignedDoctorName,
            'date' => Carbon::parse($appointment->appointment_date)->format('M d, Y'),
            'time' => $appointment->appointment_time,
            'type' => $appointment->appointment_type,
            'location' => 'Apex Main Facility - Room 302',
            'phone' => $patient->phone,
        ]);
    }
}
