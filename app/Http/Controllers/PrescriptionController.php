<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicNotification;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['patient', 'doctor', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('prescription_number', 'like', "%{$search}%")
                    ->orWhere('diagnosis', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('patient_id', 'like', "%{$search}%");
                    });
            });
        }

        $prescriptions = $query->orderBy('prescription_date', 'desc')->paginate(15)->withQueryString();

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        $patients = Patient::where('status', 'Active')->orderBy('first_name')->get();
        $selectedPatientId = $request->query('patient_id');
        $consultationId = $request->query('consultation_id');
        $appointmentId = $request->query('appointment_id');

        $consultation = $consultationId ? Consultation::find($consultationId) : null;
        $appointment = $appointmentId ? Appointment::find($appointmentId) : null;

        return view('prescriptions.create', compact('patients', 'selectedPatientId', 'consultation', 'appointment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'prescription_date' => 'required|date',
            'diagnosis' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
            'advice' => 'nullable|string',
            'tests_recommended' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'medicines' => 'required|array|min:1',
            'medicines.*.name' => 'required|string',
            'medicines.*.dosage' => 'nullable|string',
            'medicines.*.frequency' => 'nullable|string',
            'medicines.*.duration' => 'nullable|string',
            'medicines.*.route' => 'nullable|string',
            'medicines.*.timing' => 'nullable|string',
            'medicines.*.instructions' => 'nullable|string',
        ]);

        $latest = Prescription::latest('id')->first();
        $nextNum = $latest ? $latest->id + 1 : 1;
        $rxNumber = 'RX-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $prescription = Prescription::create([
            'prescription_number' => $rxNumber,
            'patient_id' => $request->patient_id,
            'doctor_id' => auth()->id(),
            'consultation_id' => $request->consultation_id,
            'appointment_id' => $request->appointment_id,
            'prescription_date' => $request->prescription_date,
            'diagnosis' => $request->diagnosis,
            'symptoms' => $request->symptoms,
            'clinical_notes' => $request->clinical_notes,
            'advice' => $request->advice,
            'tests_recommended' => $request->tests_recommended,
            'follow_up_date' => $request->follow_up_date,
        ]);

        foreach ($request->medicines as $med) {
            if (!empty($med['name'])) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_name' => $med['name'],
                    'dosage' => $med['dosage'] ?? null,
                    'frequency' => $med['frequency'] ?? null,
                    'duration' => $med['duration'] ?? null,
                    'route' => $med['route'] ?? null,
                    'timing' => $med['timing'] ?? null,
                    'instructions' => $med['instructions'] ?? null,
                ]);
            }
        }

        // Notify staff
        $patient = Patient::find($request->patient_id);
        ClinicNotification::createNotification(
            auth()->id(),
            'prescription_created',
            'Prescription Created',
            "Prescription {$rxNumber} created for patient {$patient->full_name}.",
            "/prescriptions/{$prescription->id}"
        );

        // Notify patient portal account
        if ($patient) {
            \App\Models\PatientNotification::createNotification(
                $patient->id,
                'prescription_ready',
                'New Prescription Issued',
                "Prescription #{$rxNumber} has been issued by your doctor. You can view your prescribed medicines in your patient portal.",
                "/patient/dashboard"
            );
        }

        if ($request->has('create_invoice') && $prescription->appointment_id) {
            return redirect()->route('invoices.create', [
                'patient_id' => $prescription->patient_id,
                'appointment_id' => $prescription->appointment_id,
            ])->with('success', "Prescription {$rxNumber} created. Now generate invoice.");
        }

        return redirect()->route('prescriptions.show', $prescription->id)
            ->with('success', "Prescription {$rxNumber} created successfully.");
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'items', 'consultation', 'appointment']);
        $settings = Setting::all()->pluck('value', 'key');
        return view('prescriptions.show', compact('prescription', 'settings'));
    }

    public function print(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'items']);
        $settings = Setting::all()->pluck('value', 'key');
        return view('prescriptions.print', compact('prescription', 'settings'));
    }

    public function sendToPatient(Prescription $prescription)
    {
        $prescription->load('patient');
        if ($prescription->patient) {
            \App\Models\PatientNotification::createNotification(
                $prescription->patient->id,
                'prescription_ready',
                'Prescription Sent',
                "Doctor has sent prescription #{$prescription->prescription_number} directly to your patient portal account inbox.",
                "/patient/dashboard"
            );
        }

        return back()->with('success', "Prescription {$prescription->prescription_number} sent to patient portal account inbox.");
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('prescriptions.index')->with('success', 'Prescription deleted.');
    }
}
