<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('age_group')) {
            if ($request->age_group === '0-18') {
                $query->where('age', '<=', 18);
            } elseif ($request->age_group === '19-40') {
                $query->whereBetween('age', [19, 40]);
            } elseif ($request->age_group === '41-60') {
                $query->whereBetween('age', [41, 60]);
            } elseif ($request->age_group === '60+') {
                $query->where('age', '>', 60);
            }
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        if ($request->has('date_of_birth') && !$request->has('dob')) {
            $request->merge(['dob' => $request->date_of_birth]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'required|string',
            'blood_group' => 'nullable|string',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
            'allergies' => 'nullable|string',
            'existing_conditions' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'previous_history' => 'nullable|string',
            'family_history' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:30',
        ]);

        if (!isset($validated['age']) || empty($validated['age'])) {
            if (!empty($validated['dob'])) {
                $validated['age'] = \Carbon\Carbon::parse($validated['dob'])->age;
            }
        }

        // Generate Patient ID (PAT-YYYY-XXXX)
        $latest = Patient::latest('id')->first();
        $nextNum = $latest ? $latest->id + 1 : 1;
        $patientId = 'PAT-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $validated['patient_id'] = $patientId;
        $validated['status'] = 'Active';

        $patient = Patient::create($validated);

        return redirect()->route('patients.show', $patient->id)
            ->with('success', "Patient {$patient->full_name} registered successfully with ID: {$patient->patient_id}");
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'appointments.doctor',
            'consultations.doctor',
            'prescriptions.items',
            'prescriptions.doctor',
            'invoices.items',
            'invoices.payments',
            'payments',
            'documents',
        ]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'required|string',
            'blood_group' => 'nullable|string',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
            'allergies' => 'nullable|string',
            'existing_conditions' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'previous_history' => 'nullable|string',
            'family_history' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'status' => 'required|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'Patient record updated successfully.');
    }

    public function uploadDocument(Request $request, Patient $patient)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('patient_documents/' . $patient->id, $filename, 'public');

            $doc = PatientDocument::create([
                'patient_id' => $patient->id,
                'title' => $request->title,
                'file_path' => '/storage/' . $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'notes' => $request->notes,
            ]);

            // Automatically notify patient about new lab report / document
            $isLabReport = preg_match('/lab|report|test|blood|scan|x-ray|mri|pathology|biopsy/i', $request->title . ' ' . ($request->notes ?? ''));
            $notifType = $isLabReport ? 'lab_report_ready' : 'general';
            $notifTitle = $isLabReport ? 'New Lab Report Available: ' . $request->title : 'New Document Uploaded: ' . $request->title;
            
            \App\Models\PatientNotification::create([
                'patient_id' => $patient->id,
                'type' => $notifType,
                'title' => $notifTitle,
                'message' => 'A new document ("' . $request->title . '") has been uploaded to your patient portal. You can view or download it directly.',
                'link' => '/storage/' . $path,
                'is_read' => false,
            ]);

            return back()->with('success', 'Document uploaded successfully and patient notified.');
        }

        return back()->with('error', 'Failed to upload document.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }
}
