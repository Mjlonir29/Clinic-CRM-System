<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $adminRole = Role::where('slug', 'admin')->first();

        // Fetch doctors (admin role or users designated as doctors)
        $doctors = User::where(function($q) use ($adminRole) {
            $q->where('role_slug', 'admin');
            if ($adminRole) {
                $q->orWhere('role_id', $adminRole->id);
            }
        })->get()->map(function($doctor) {
            $doctor->doctor_id = 'DOC-' . date('Y') . '-' . str_pad($doctor->id, 4, '0', STR_PAD_LEFT);
            $doctor->visit_count = Appointment::where('doctor_id', $doctor->id)->count();
            $doctor->today_visit_count = Appointment::where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', now()->format('Y-m-d'))
                ->count();
            return $doctor;
        });

        // Track doctor visits by date
        $visitsByDate = Appointment::selectRaw('appointment_date, doctor_id, COUNT(*) as visit_count')
            ->groupBy('appointment_date', 'doctor_id')
            ->orderBy('appointment_date', 'desc')
            ->with(['doctor', 'patient'])
            ->get();

        return view('doctors.index', compact('doctors', 'visitsByDate'));
    }

    public function create()
    {
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'qualifications' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'registration_number' => 'required|string|max:100',
            'experience_years' => 'nullable|string|max:50',
            'consultation_fee' => 'required|numeric|min:0',
            'cabin_number' => 'nullable|string|max:100',
            'working_hours' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $adminRole = Role::where('slug', 'admin')->first();

        $doctor = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'qualifications' => $request->qualifications,
            'specialization' => $request->specialization,
            'registration_number' => $request->registration_number,
            'experience_years' => $request->experience_years,
            'consultation_fee' => $request->consultation_fee,
            'cabin_number' => $request->cabin_number,
            'working_hours' => $request->working_hours,
            'password' => Hash::make($request->password),
            'role_id' => $adminRole ? $adminRole->id : 1,
            'role_slug' => 'admin',
            'status' => 'active',
        ]);

        return redirect()->route('doctors.index')
            ->with('success', "Doctor {$doctor->name} added successfully!");
    }

    public function show(User $doctor)
    {
        $doctor->doctor_id = 'DOC-' . date('Y') . '-' . str_pad($doctor->id, 4, '0', STR_PAD_LEFT);
        $doctor->visit_count = Appointment::where('doctor_id', $doctor->id)->count();
        $doctor->today_visit_count = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', now()->format('Y-m-d'))
            ->count();

        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->with('patient')
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

        return view('doctors.show', compact('doctor', 'appointments'));
    }
}
