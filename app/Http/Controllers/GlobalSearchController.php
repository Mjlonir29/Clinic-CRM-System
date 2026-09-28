<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->input('q'));

        if (empty($query)) {
            return response()->json([
                'patients' => [],
                'appointments' => [],
                'prescriptions' => [],
                'invoices' => [],
            ]);
        }

        $patients = Patient::where('patient_id', 'like', "%{$query}%")
            ->orWhere('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'patient_id', 'first_name', 'last_name', 'phone', 'gender', 'age']);

        $appointments = Appointment::with('patient')
            ->where('appointment_number', 'like', "%{$query}%")
            ->orWhereHas('patient', function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            })
            ->take(5)
            ->get();

        $prescriptions = Prescription::with('patient')
            ->where('prescription_number', 'like', "%{$query}%")
            ->orWhere('diagnosis', 'like', "%{$query}%")
            ->orWhereHas('patient', function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->take(5)
            ->get();

        $invoices = Invoice::with('patient')
            ->where('invoice_number', 'like', "%{$query}%")
            ->orWhereHas('patient', function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->take(5)
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'patients' => $patients,
                'appointments' => $appointments,
                'prescriptions' => $prescriptions,
                'invoices' => $invoices,
            ]);
        }

        return view('search.results', compact('query', 'patients', 'appointments', 'prescriptions', 'invoices'));
    }
}
