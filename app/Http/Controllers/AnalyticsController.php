<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\PatientFeedback;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Monthly Revenue Breakdown (Last 6 Months)
        $revenueMonths = [];
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueMonths[] = $date->format('M Y');
            $sum = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $revenueData[] = (float) $sum;
        }

        // 2. Top Clinical Services Billed
        $topServices = InvoiceItem::select('service_name', DB::raw('SUM(total) as total_revenue'), DB::raw('COUNT(*) as total_count'))
            ->groupBy('service_name')
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();

        // 3. Peak Patient Visit Hours (Distribution by appointment time)
        $hourDistribution = Appointment::select(DB::raw('HOUR(appointment_time) as visit_hour'), DB::raw('COUNT(*) as count'))
            ->whereNotNull('appointment_time')
            ->groupBy('visit_hour')
            ->orderBy('visit_hour')
            ->pluck('count', 'visit_hour')
            ->toArray();

        // 4. Doctor Performance Matrix
        $doctors = User::where('role_slug', 'admin')->orWhereNotNull('specialization')->get();
        $doctorStats = [];
        foreach ($doctors as $doc) {
            $consultationsCount = Appointment::where('doctor_id', $doc->id)->where('status', 'Completed')->count();
            if ($consultationsCount === 0) {
                $consultationsCount = Appointment::where('doctor_id', $doc->id)->count();
            }
            $revenueGenerated = Invoice::where('doctor_id', $doc->id)->sum('total_amount');
            $avgRating = PatientFeedback::where('doctor_id', $doc->id)->avg('rating') ?: 5.0;

            $doctorStats[] = [
                'doctor' => $doc,
                'consultations_count' => $consultationsCount,
                'revenue_generated' => (float) $revenueGenerated,
                'rating' => round($avgRating, 1),
            ];
        }

        // 5. Patient Retention Insights
        $totalPatients = Patient::count();
        $returningPatients = Patient::has('appointments', '>', 1)->count();
        $newPatients = max($totalPatients - $returningPatients, 0);
        $retentionRate = $totalPatients > 0 ? round(($returningPatients / $totalPatients) * 100, 1) : 100;

        $completedAppointments = Appointment::where('status', 'Completed')->count();
        $totalAppointments = Appointment::count();
        $followupComplianceRate = $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100, 1) : 100;

        return view('analytics.index', compact(
            'revenueMonths',
            'revenueData',
            'topServices',
            'hourDistribution',
            'doctorStats',
            'totalPatients',
            'newPatients',
            'returningPatients',
            'retentionRate',
            'followupComplianceRate'
        ));
    }
}
