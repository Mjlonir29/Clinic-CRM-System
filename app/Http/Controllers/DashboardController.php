<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role_slug === 'nurse') {
            return redirect()->route('patients.index');
        }

        $today = Carbon::today()->format('Y-m-d');
        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

        // Dynamic Statistics Cards from Database
        $totalPatients = Patient::count();
        $todaysAppointmentsCount = Appointment::where('appointment_date', $today)->count();
        $upcomingAppointmentsCount = Appointment::where('appointment_date', '>=', $today)
            ->whereIn('status', ['Pending', 'Confirmed', 'Checked In'])
            ->count();
        $completedAppointmentsCount = Appointment::where('status', 'Completed')->count();
        $pendingAppointmentsCount = Appointment::where('status', 'Pending')->count();
        $cancelledAppointmentsCount = Appointment::where('status', 'Cancelled')->count();

        $totalInvoicesCount = Invoice::count();
        $paidAmountTotal = Payment::sum('amount');
        $pendingAmountTotal = Invoice::whereIn('status', ['Unpaid', 'Partially Paid'])->sum('due_amount');

        // Dynamic Today's / Active Appointments List
        $todaysAppointments = Appointment::with(['patient', 'doctor'])
            ->where('appointment_date', $today)
            ->orderBy('appointment_time', 'asc')
            ->get();

        if ($todaysAppointments->isEmpty()) {
            $todaysAppointments = Appointment::with(['patient', 'doctor'])
                ->latest()
                ->take(10)
                ->get();
        }

        // Dynamic Recent Patients List
        $recentPatients = Patient::latest()
            ->take(6)
            ->get();

        // Dynamic Revenue Overview
        $todayRevenue = Payment::where('payment_date', $today)->sum('amount');
        $thisWeekRevenue = Payment::where('payment_date', '>=', $startOfWeek)->sum('amount');
        $thisMonthRevenue = Payment::where('payment_date', '>=', $startOfMonth)->sum('amount');
        if ($thisMonthRevenue == 0) {
            $thisMonthRevenue = $paidAmountTotal;
        }

        // Dynamic Monthly revenue chart data (last 6 months)
        $chartMonths = [];
        $chartRevenues = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthName = $monthDate->format('M Y');
            $rev = Payment::whereYear('payment_date', $monthDate->year)
                ->whereMonth('payment_date', $monthDate->month)
                ->sum('amount');

            $chartMonths[] = $monthName;
            $chartRevenues[] = (float) $rev;
        }

        return view('dashboard', compact(
            'totalPatients',
            'todaysAppointmentsCount',
            'upcomingAppointmentsCount',
            'completedAppointmentsCount',
            'pendingAppointmentsCount',
            'cancelledAppointmentsCount',
            'totalInvoicesCount',
            'paidAmountTotal',
            'pendingAmountTotal',
            'todaysAppointments',
            'recentPatients',
            'todayRevenue',
            'thisWeekRevenue',
            'thisMonthRevenue',
            'chartMonths',
            'chartRevenues'
        ));
    }
}
