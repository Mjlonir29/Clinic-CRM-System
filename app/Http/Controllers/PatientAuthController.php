<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientNotification;
use App\Services\JWTService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientAuthController extends Controller
{
    /**
     * Show Patient Login Form.
     */
    public function showLogin()
    {
        return view('patient_portal.login');
    }

    /**
     * Authenticate Patient and generate JWT.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->login);
        $passwordInput = trim($request->password);

        // Find patient by Patient ID, Email, or Phone
        $patient = Patient::where('patient_id', $loginInput)
            ->orWhere('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->first();

        if (!$patient) {
            return back()->withInput()->with('error', 'No patient record found matching the provided Patient ID, Phone, or Email.');
        }

        // Check password logic
        $isAuthenticated = false;

        if ($patient->password) {
            // Verify hashed password
            if (Hash::check($passwordInput, $patient->password)) {
                $isAuthenticated = true;
            }
        } else {
            // First time login or fallback matching: check DOB (YYYY-MM-DD or DD/MM/YYYY) or Phone
            $dobFormatted = $patient->dob ? date('Y-m-d', strtotime($patient->dob)) : null;
            $dobAlt = $patient->dob ? date('d-m-Y', strtotime($patient->dob)) : null;
            $dobAlt2 = $patient->dob ? date('dmY', strtotime($patient->dob)) : null;

            if (
                $passwordInput === $patient->phone ||
                $passwordInput === $patient->patient_id ||
                ($dobFormatted && $passwordInput === $dobFormatted) ||
                ($dobAlt && $passwordInput === $dobAlt) ||
                ($dobAlt2 && $passwordInput === $dobAlt2)
            ) {
                $isAuthenticated = true;
                // Automatically hash & store password for future logins
                $patient->password = Hash::make($passwordInput);
                $patient->save();
            }
        }

        if (!$isAuthenticated) {
            return back()->withInput()->with('error', 'Invalid password or verification credential. Default password is your Date of Birth (YYYY-MM-DD) or Phone Number.');
        }

        // Generate JWT Token
        $jwtToken = JWTService::generateToken($patient);

        // Put in session & queue cookie
        session(['patient_jwt_token' => $jwtToken, 'patient_id' => $patient->id]);
        $cookie = cookie('patient_jwt', $jwtToken, 60 * 24 * 30, null, null, false, true);

        return redirect()->route('patient_portal.dashboard')
            ->withCookie($cookie)
            ->with('success', "Welcome back, {$patient->full_name}!");
    }

    /**
     * Patient Dashboard - Protected by JWT.
     */
    public function dashboard(Request $request)
    {
        $patient = $this->getAuthenticatedPatient($request);

        if (!$patient) {
            return redirect()->route('patient_portal.login')
                ->with('error', 'Session expired or unauthenticated. Please sign in with your JWT credentials.');
        }

        // Auto-check and generate notifications for upcoming appointments if missing
        $this->syncUpcomingAppointmentNotifications($patient);

        // Load relations
        $patient->load([
            'appointments.doctor',
            'consultations.doctor',
            'prescriptions.items',
            'prescriptions.doctor',
            'invoices.items',
            'documents',
            'notifications' => function ($q) {
                $q->latest();
            }
        ]);

        $unreadCount = $patient->notifications->where('is_read', false)->count();

        return view('patient_portal.dashboard', compact('patient', 'unreadCount'));
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead(Request $request, $id)
    {
        $patient = $this->getAuthenticatedPatient($request);
        if (!$patient) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $notification = PatientNotification::where('patient_id', $patient->id)->where('id', $id)->first();
        if ($notification) {
            $notification->is_read = true;
            $notification->save();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Logout Patient.
     */
    public function logout(Request $request)
    {
        session()->forget(['patient_jwt_token', 'patient_id']);
        $cookie = cookie()->forget('patient_jwt');

        return redirect()->route('patient_portal.login')
            ->withCookie($cookie)
            ->with('success', 'You have been logged out safely.');
    }

    /**
     * Helper to authenticate patient via JWT token in cookie, header, or session.
     */
    private function getAuthenticatedPatient(Request $request): ?Patient
    {
        $token = $request->cookie('patient_jwt')
            ?? session('patient_jwt_token')
            ?? $request->bearerToken();

        $payload = JWTService::validateToken($token);
        if (!$payload || !isset($payload['sub'])) {
            return null;
        }

        return Patient::find($payload['sub']);
    }

    /**
     * Check upcoming appointments (within next 7 days or today) and create notifications if not exists.
     */
    private function syncUpcomingAppointmentNotifications(Patient $patient): void
    {
        $today = now()->format('Y-m-d');
        $nextWeek = now()->addDays(7)->format('Y-m-d');

        $upcomingAppointments = $patient->appointments()
            ->whereBetween('appointment_date', [$today, $nextWeek])
            ->whereIn('status', ['Pending', 'Confirmed'])
            ->get();

        foreach ($upcomingAppointments as $appointment) {
            $notifTitle = "Upcoming Appointment: " . date('d M Y', strtotime($appointment->appointment_date)) . " (" . $appointment->appointment_time . ")";
            
            $exists = PatientNotification::where('patient_id', $patient->id)
                ->where('type', 'appointment_reminder')
                ->where('title', $notifTitle)
                ->exists();

            if (!$exists) {
                $docName = $appointment->doctor ? $appointment->doctor->name : 'Clinic Specialist';
                PatientNotification::create([
                    'patient_id' => $patient->id,
                    'type' => 'appointment_reminder',
                    'title' => $notifTitle,
                    'message' => "You have a scheduled appointment with {$docName} on {$appointment->appointment_date} at {$appointment->appointment_time}. Status: {$appointment->status}.",
                    'link' => '#appointments-section',
                    'is_read' => false,
                ]);
            }
        }
    }
}
