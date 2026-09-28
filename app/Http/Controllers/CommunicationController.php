<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\CommunicationLog;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::orderBy('first_name')->get();
        $appointments = Appointment::with('patient')->latest()->take(10)->get();
        $invoices = Invoice::with('patient')->latest()->take(10)->get();
        $prescriptions = Prescription::with('patient')->latest()->take(10)->get();

        $logs = CommunicationLog::with('patient')->orderBy('created_at', 'desc')->paginate(15);

        return view('communication.index', compact('patients', 'appointments', 'invoices', 'prescriptions', 'logs'));
    }

    public function sendWhatsApp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'template_type' => 'required|string',
            'message' => 'required|string',
            'patient_id' => 'nullable|exists:patients,id',
        ]);

        $log = CommunicationLog::create([
            'patient_id' => $request->patient_id,
            'recipient_phone' => $request->phone,
            'channel' => 'WhatsApp',
            'template_type' => $request->template_type,
            'message_content' => $request->message,
            'status' => 'Sent',
        ]);

        AuditLog::record("Sent WhatsApp message to {$request->phone} ({$request->template_type})", "WhatsApp & SMS");

        // Clean phone number for WhatsApp URL
        $phoneClean = preg_replace('/[^0-9]/', '', $request->phone);
        if (strlen($phoneClean) == 10) {
            $phoneClean = '91' . $phoneClean; // Default Indian country code prefix if 10 digits
        }

        $waUrl = "https://wa.me/{$phoneClean}?text=" . urlencode($request->message);

        return redirect()->away($waUrl);
    }

    public function logMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'template_type' => 'required|string',
            'message' => 'required|string',
            'patient_id' => 'nullable|exists:patients,id',
        ]);

        CommunicationLog::create([
            'patient_id' => $request->patient_id,
            'recipient_phone' => $request->phone,
            'channel' => 'WhatsApp / SMS',
            'template_type' => $request->template_type,
            'message_content' => $request->message,
            'status' => 'Delivered',
        ]);

        return back()->with('success', 'Communication notification logged successfully.');
    }
}
