<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicNotification;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('appointment_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('patient_id', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date')) {
            $query->where('appointment_date', $request->date);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(15)
            ->withQueryString();

        $todayDate = Carbon::today()->format('Y-m-d');
        $totalToday = Appointment::where('appointment_date', $todayDate)->count();
        $waitingToday = Appointment::where('appointment_date', $todayDate)->whereIn('status', ['Pending', 'Confirmed', 'Checked In'])->count();
        $consultingToday = Appointment::where('appointment_date', $todayDate)->where('status', 'In Consultation')->count();
        $completedToday = Appointment::where('appointment_date', $todayDate)->where('status', 'Completed')->count();

        $doctors = User::whereIn('role_slug', ['admin', 'doctor'])->get();
        $patients = Patient::where('status', 'Active')->orderBy('first_name')->get();

        return view('appointments.index', compact('appointments', 'doctors', 'patients', 'totalToday', 'waitingToday', 'consultingToday', 'completedToday'));
    }

    public function create(Request $request)
    {
        $patients = Patient::where('status', 'Active')->orderBy('first_name')->get();
        $doctors = User::whereIn('role_slug', ['admin', 'doctor'])->get();
        $selectedPatientId = $request->query('patient_id');
        $selectedDoctorId = $request->query('doctor_id');

        return view('appointments.create', compact('patients', 'doctors', 'selectedPatientId', 'selectedDoctorId'));
    }

    public function calendar(Request $request)
    {
        $view = $request->input('view', 'month');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);

        if ($view === 'day') {
            $startDate = $carbonDate->copy()->startOfDay();
            $endDate = $carbonDate->copy()->endOfDay();
        } elseif ($view === 'week') {
            $startDate = $carbonDate->copy()->startOfWeek();
            $endDate = $carbonDate->copy()->endOfWeek();
        } else { // month
            $startDate = $carbonDate->copy()->startOfMonth()->subDays(7);
            $endDate = $carbonDate->copy()->endOfMonth()->addDays(7);
        }

        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $patients = Patient::where('status', 'Active')->orderBy('first_name')->get();
        $doctors = User::whereIn('role_slug', ['admin', 'doctor'])->get();

        return view('appointments.calendar', compact('appointments', 'view', 'date', 'carbonDate', 'patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'appointment_type' => 'required|string',
            'reason_for_visit' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_status' => 'required|string',
            'status' => 'required|string',
        ]);

        $latest = Appointment::latest('id')->first();
        $nextNum = $latest ? $latest->id + 1 : 1;
        $validated['appointment_number'] = 'APT-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        if (empty($validated['doctor_id'])) {
            $doctor = User::where('role_slug', 'admin')->first();
            $validated['doctor_id'] = $doctor ? $doctor->id : auth()->id();
        }

        $appointment = Appointment::create($validated);

        // Notify doctor/admin
        $patient = Patient::find($appointment->patient_id);
        ClinicNotification::createNotification(
            $appointment->doctor_id,
            'appointment_created',
            'New Appointment Scheduled',
            "Appointment {$appointment->appointment_number} created for {$patient->full_name} on {$appointment->appointment_date} at {$appointment->appointment_time}.",
            "/appointments"
        );

        // Notify patient
        if ($patient) {
            \App\Models\PatientNotification::createNotification(
                $patient->id,
                'appointment_reminder',
                'Appointment Scheduled',
                "Your appointment (#{$appointment->appointment_number}) has been scheduled for {$appointment->appointment_date} at {$appointment->appointment_time}. Status: {$appointment->status}.",
                "#appointments-section"
            );
        }

        return back()->with('success', "Appointment {$appointment->appointment_number} scheduled successfully.");
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'consultation', 'prescription.items', 'invoice.payments']);
        return view('appointments.show', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'appointment_type' => 'required|string',
            'reason_for_visit' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_status' => 'required|string',
            'status' => 'required|string',
        ]);

        $appointment->update($validated);

        if ($appointment->patient_id) {
            \App\Models\PatientNotification::createNotification(
                $appointment->patient_id,
                'appointment_reminder',
                'Appointment Updated',
                "Your appointment (#{$appointment->appointment_number}) has been updated to {$appointment->appointment_date} at {$appointment->appointment_time}. Status: {$appointment->status}.",
                "#appointments-section"
            );
        }

        return back()->with('success', "Appointment {$appointment->appointment_number} updated.");
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $appointment->update(['status' => $request->status]);

        if ($appointment->patient_id) {
            \App\Models\PatientNotification::createNotification(
                $appointment->patient_id,
                'appointment_reminder',
                'Appointment Status Changed',
                "Your appointment (#{$appointment->appointment_number}) status is now: {$request->status}.",
                "#appointments-section"
            );
        }

        return back()->with('success', "Appointment status changed to {$request->status}.");
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }
}
