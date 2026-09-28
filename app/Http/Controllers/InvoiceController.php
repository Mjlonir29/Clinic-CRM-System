<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicNotification;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['patient', 'doctor', 'items', 'payments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('patient_id', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15)->withQueryString();

        // Fully Dynamic Billing & Invoice Metrics
        $thisMonthInvoiced = Invoice::whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('total_amount');
        if ($thisMonthInvoiced == 0) {
            $thisMonthInvoiced = Invoice::sum('total_amount');
        }

        $thisMonthInvoicedCount = Invoice::whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->count();
        if ($thisMonthInvoicedCount == 0) {
            $thisMonthInvoicedCount = Invoice::count();
        }

        $receivedRevenue = Payment::sum('amount');
        $cardUpiPayments = Payment::whereIn('payment_method', ['UPI', 'Card', 'Online', 'PhonePe', 'Google Pay'])->sum('amount');
        $cashPayments = Payment::where('payment_method', 'Cash')->sum('amount');

        $pendingDue = Invoice::sum('due_amount');
        $pendingInvoicesCount = Invoice::where('due_amount', '>', 0)->count();

        $overdueInvoicesCount = Invoice::where('due_date', '<', now()->format('Y-m-d'))
            ->where('due_amount', '>', 0)
            ->count();
        $overdueAmount = Invoice::where('due_date', '<', now()->format('Y-m-d'))
            ->where('due_amount', '>', 0)
            ->sum('due_amount');

        return view('invoices.index', compact(
            'invoices',
            'thisMonthInvoiced',
            'thisMonthInvoicedCount',
            'receivedRevenue',
            'cardUpiPayments',
            'cashPayments',
            'pendingDue',
            'pendingInvoicesCount',
            'overdueInvoicesCount',
            'overdueAmount'
        ));
    }

    public function create(Request $request)
    {
        $patients = Patient::where('status', 'Active')->orderBy('first_name')->get();
        $selectedPatientId = $request->query('patient_id');
        $appointmentId = $request->query('appointment_id');

        $defaultConsultationFee = Setting::get('consultation_fee', '500.00');
        $taxPercentage = Setting::get('tax_percentage', '5.0');

        return view('invoices.create', compact(
            'patients',
            'selectedPatientId',
            'appointmentId',
            'defaultConsultationFee',
            'taxPercentage'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $latest = Invoice::latest('id')->first();
        $nextNum = $latest ? $latest->id + 1 : 1;
        $invNumber = 'INV-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'invoice_number' => $invNumber,
            'patient_id' => $request->patient_id,
            'appointment_id' => $request->appointment_id,
            'doctor_id' => auth()->id(),
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date ?? $request->invoice_date,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'status' => 'Unpaid',
            'notes' => $request->notes,
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $qty = (int) $item['quantity'];
            $rate = (float) $item['rate'];
            $lineTotal = $qty * $rate;
            $subtotal += $lineTotal;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_name' => $item['service_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $qty,
                'rate' => $rate,
                'discount' => 0,
                'tax' => 0,
                'total' => $lineTotal,
            ]);
        }

        $invoice->subtotal = $subtotal;
        $invoice->recalculateTotals();

        // Notify staff
        $patient = Patient::find($request->patient_id);
        ClinicNotification::createNotification(
            auth()->id(),
            'invoice_generated',
            'Invoice Generated',
            "Invoice {$invNumber} for ₹" . number_format($invoice->total_amount, 2) . " created for {$patient->full_name}.",
            "/invoices/{$invoice->id}"
        );

        // Notify patient portal
        if ($patient) {
            \App\Models\PatientNotification::createNotification(
                $patient->id,
                'general',
                'New Invoice Generated',
                "Invoice #{$invNumber} for ₹" . number_format($invoice->total_amount, 2) . " has been issued for your consultation/services.",
                "/patient/dashboard"
            );
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', "Invoice {$invNumber} created successfully.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'doctor', 'items', 'payments', 'appointment']);
        $settings = Setting::all()->pluck('value', 'key');
        return view('invoices.show', compact('invoice', 'settings'));
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($invoice->due_amount > 0 ? $invoice->due_amount : $invoice->total_amount),
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $latestPay = Payment::latest('id')->first();
        $nextNum = $latestPay ? $latestPay->id + 1 : 1;
        $payNumber = 'PAY-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        Payment::create([
            'payment_number' => $payNumber,
            'invoice_id' => $invoice->id,
            'patient_id' => $invoice->patient_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
            'notes' => $request->notes,
        ]);

        $invoice->recalculateTotals();

        // If invoice fully paid and tied to an appointment, mark appointment completed
        if ($invoice->status === 'Paid' && $invoice->appointment) {
            $invoice->appointment->update([
                'status' => 'Completed',
                'payment_status' => 'Paid',
            ]);
        }

        // Notify staff
        ClinicNotification::createNotification(
            auth()->id(),
            'payment_received',
            'Payment Recorded',
            "Recorded payment of ₹" . number_format($request->amount, 2) . " for Invoice {$invoice->invoice_number}.",
            "/invoices/{$invoice->id}"
        );

        // Notify patient portal account
        if ($invoice->patient_id) {
            \App\Models\PatientNotification::createNotification(
                $invoice->patient_id,
                'general',
                'Payment Receipt Confirmation',
                "Payment of ₹" . number_format($request->amount, 2) . " received for Invoice #{$invoice->invoice_number}. Current Status: {$invoice->status}.",
                "/patient/dashboard"
            );
        }

        return back()->with('success', "Payment of ₹" . number_format($request->amount, 2) . " recorded successfully.");
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['patient', 'doctor', 'items', 'payments']);
        $settings = Setting::all()->pluck('value', 'key');
        return view('invoices.print', compact('invoice', 'settings'));
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:Paid,Unpaid,Partially Paid,Cancelled',
        ]);

        $newStatus = $request->status;
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'Paid') {
            $updateData['paid_amount'] = $invoice->total_amount;
            $updateData['due_amount'] = 0;
            if ($invoice->appointment) {
                $invoice->appointment->update(['status' => 'Completed', 'payment_status' => 'Paid']);
            }
        } elseif ($newStatus === 'Unpaid') {
            $updateData['paid_amount'] = 0;
            $updateData['due_amount'] = $invoice->total_amount;
            if ($invoice->appointment) {
                $invoice->appointment->update(['payment_status' => 'Unpaid']);
            }
        } elseif ($newStatus === 'Cancelled') {
            if ($invoice->appointment) {
                $invoice->appointment->update(['payment_status' => 'Cancelled']);
            }
        }

        $invoice->update($updateData);

        ClinicNotification::createNotification(
            auth()->id(),
            'invoice_updated',
            'Invoice Status Updated',
            "Invoice {$invoice->invoice_number} status updated to {$newStatus}.",
            "/invoices/{$invoice->id}"
        );

        return back()->with('success', "Invoice {$invoice->invoice_number} status updated to {$newStatus}.");
    }

    public function cancel(Invoice $invoice)
    {
        $invoice->update(['status' => 'Cancelled']);
        if ($invoice->appointment) {
            $invoice->appointment->update(['payment_status' => 'Cancelled']);
        }
        return back()->with('success', "Invoice {$invoice->invoice_number} cancelled.");
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}
