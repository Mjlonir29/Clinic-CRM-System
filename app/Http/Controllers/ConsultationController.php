<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function create(Request $request)
    {
        $appointmentId = $request->query('appointment_id');
        $patientId = $request->query('patient_id');

        $appointment = null;
        if ($appointmentId) {
            $appointment = Appointment::with('patient')->findOrFail($appointmentId);
            $patient = $appointment->patient;

            // Automatically update status to 'In Consultation' if coming from checked-in/confirmed
            if (in_array($appointment->status, ['Pending', 'Confirmed', 'Checked In'])) {
                $appointment->update(['status' => 'In Consultation']);
            }
        } else {
            $patient = Patient::findOrFail($patientId);
        }

        return view('consultations.create', compact('appointment', 'patient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'required|string',
            'medical_notes' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'bp' => 'nullable|string',
            'pulse' => 'nullable|string',
            'temp' => 'nullable|string',
            'weight' => 'nullable|string',
            'height' => 'nullable|string',
            'spo2' => 'nullable|string',
        ]);

        $vitalSigns = array_filter([
            'bp' => $request->bp,
            'pulse' => $request->pulse,
            'temp' => $request->temp,
            'weight' => $request->weight,
            'height' => $request->height,
            'spo2' => $request->spo2,
        ]);

        $consultation = Consultation::create([
            'appointment_id' => $request->appointment_id,
            'patient_id' => $request->patient_id,
            'doctor_id' => auth()->id(),
            'consultation_date' => Carbon::today()->format('Y-m-d'),
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'medical_notes' => $request->medical_notes,
            'treatment_plan' => $request->treatment_plan,
            'vital_signs' => $vitalSigns,
        ]);

        if ($request->filled('appointment_id')) {
            Appointment::where('id', $request->appointment_id)->update(['status' => 'Completed']);
        }

        // Notify patient
        $patient = Patient::find($consultation->patient_id);
        $doctor = auth()->user();
        $docName = $doctor ? $doctor->name : 'Doctor';

        if ($patient) {
            \App\Models\PatientNotification::createNotification(
                $patient->id,
                'general',
                'Consultation Completed',
                "Your consultation with {$docName} has been completed. Diagnosis: {$consultation->diagnosis}.",
                "/patient/dashboard"
            );
        }

        // Notify clinic team
        \App\Models\ClinicNotification::createNotification(
            auth()->id(),
            'consultation_completed',
            'Consultation Completed',
            "Consultation recorded for {$patient->full_name} by {$docName}.",
            "/patients/{$patient->id}"
        );

        // If user selected "Save & Create Prescription"
        if ($request->has('create_prescription')) {
            return redirect()->route('prescriptions.create', [
                'patient_id' => $consultation->patient_id,
                'consultation_id' => $consultation->id,
                'appointment_id' => $consultation->appointment_id,
            ])->with('success', 'Consultation notes saved. Now create prescription.');
        }

        return redirect()->route('patients.show', $consultation->patient_id)
            ->with('success', 'Consultation recorded successfully.');
    }
}
