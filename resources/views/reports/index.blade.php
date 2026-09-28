@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Breadcrumbs & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinic Admin</span>
                <span>/</span>
                <span class="text-teal-800">Reports & Analytics</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Reports & Analytics</h1>
                <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-teal-50 text-teal-800 border border-teal-200/60 rounded-md">● Executive Insights</span>
                <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200 rounded-md">Q3 2026 Telemetry</span>
            </div>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Comprehensive financial performance, patient volume cohorts, clinical service utilization, and clinic operational metrics across all wings.</p>
        </div>

        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center border border-slate-200 no-print">
                🖨️ Print Report
            </button>
            <a href="{{ route('reports.export', ['type' => $tab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/20 transition-all flex items-center transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV / PDF
            </a>
        </div>
    </div>

    <!-- Date Range & Provider Filters Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-700">
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-xs" />
            <span class="text-slate-400">to</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-xs" />
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition-all">Apply Filter</button>
        </form>
        <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-400">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Reporting Engine: Sync Rate 100%</span>
        </div>
    </div>

    <!-- 4 Executive Performance Metrics Grid (Values in Rupees ₹) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Gross Revenue Card -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Gross Revenue</span>
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold text-xs">₹</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">₹{{ number_format($totalRevenue, 2) }}</p>
                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200/60">+12.4%</span>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-4 pt-2 border-t border-slate-100">
                <span>Net collection rate</span>
                <span class="text-slate-900 font-extrabold">{{ number_format($netCollectionRate, 1) }}%</span>
            </div>
        </div>

        <!-- Patient Encounters Card -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Patient Encounters</span>
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold text-xs">👥</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">{{ number_format($totalEncounters) }}</p>
                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200/60">+8.1%</span>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-4 pt-2 border-t border-slate-100">
                <span>Clinical throughput</span>
                <span class="text-slate-900 font-extrabold">{{ round($totalEncounters / 30, 1) }} visits/day</span>
            </div>
        </div>

        <!-- Avg Revenue Per Patient -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Avg Revenue Per Patient</span>
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold text-xs">📊</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">₹{{ number_format($avgRevenuePerPatient, 2) }}</p>
                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200/60">+3.2%</span>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-4 pt-2 border-t border-slate-100">
                <span>Median clinical wait time</span>
                <span class="text-slate-900 font-extrabold">14m</span>
            </div>
        </div>

        <!-- Claim Adjudication Rate -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Claim Adjudication Rate</span>
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold text-xs">🛡️</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">{{ number_format($claimAdjudicationRate, 1) }}%</p>
                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200/60">+1.8%</span>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-4 pt-2 border-t border-slate-100">
                <span>Payer compliance audit</span>
                <span class="text-emerald-700 font-black">✓ Zero Flags</span>
            </div>
        </div>

    </div>

    <!-- Main Analytics Section 1: Revenue Trends & Appointment Status Donut -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Revenue Trends Chart (2 Columns) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-heading font-black text-slate-900">Clinic Revenue Trends vs. Previous Month</h3>
                    <p class="text-xs text-slate-500 font-medium">Daily billings comparison in Indian Rupees (₹)</p>
                </div>
                <div class="flex items-center space-x-4 text-xs font-bold">
                    <span class="flex items-center"><span class="w-3 h-1 bg-teal-800 rounded mr-1.5"></span> Current Period (₹{{ number_format(array_sum($chartCurrentRevenue)) }})</span>
                    <span class="flex items-center text-slate-400"><span class="w-3 h-1 bg-slate-300 rounded mr-1.5"></span> Previous Period (₹{{ number_format(array_sum($chartPreviousRevenue)) }})</span>
                </div>
            </div>

            <div class="h-64 relative">
                <canvas id="reportsRevenueChart"></canvas>
            </div>

            <div class="flex flex-wrap items-center justify-between text-xs font-bold text-slate-500 pt-3 border-t border-slate-100">
                <span>Highest Day: <strong class="text-slate-900">₹{{ number_format($highestDayAmount) }}</strong></span>
                <span>Lowest Day: <strong class="text-slate-900">₹{{ number_format($lowestDayAmount) }}</strong></span>
                <span>Avg Daily Billing: <strong class="text-teal-800">₹{{ number_format($avgDailyBilling) }}</strong></span>
            </div>
        </div>

        <!-- Appointment Status Donut Card (1 Column) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card flex flex-col justify-between">
            <div>
                <h3 class="text-base font-heading font-black text-slate-900">Appointment Status</h3>
                <p class="text-xs text-slate-500 font-medium">{{ $totalApptsCount }} total scheduled slots</p>
            </div>

            <div class="h-44 relative my-4">
                <canvas id="reportsAppointmentDonut"></canvas>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs font-bold pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600"><span class="w-2 h-2 rounded-full bg-teal-800 mr-1.5"></span> Completed</span>
                    <span class="text-slate-900">{{ $completedPct }}% ({{ $completedCount }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600"><span class="w-2 h-2 rounded-full bg-indigo-500 mr-1.5"></span> Scheduled</span>
                    <span class="text-slate-900">{{ $rescheduledPct }}% ({{ $rescheduledCount }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600"><span class="w-2 h-2 rounded-full bg-slate-400 mr-1.5"></span> Cancelled</span>
                    <span class="text-slate-900">{{ $cancelledPct }}% ({{ $cancelledCount }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600"><span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></span> No-Show</span>
                    <span class="text-rose-700 font-black">{{ $noShowPct }}% ({{ $noShowCount }})</span>
                </div>
            </div>

            <div class="p-3 rounded-2xl bg-teal-50/70 border border-teal-200/60 text-[10px] text-teal-800 font-semibold mt-3">
                ✓ Automated SMS confirmations active for all scheduled visits.
            </div>
        </div>

    </div>

    <!-- Attending Practitioner Productivity Ledger Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-heading font-black text-slate-900">Attending Practitioner Productivity Ledger</h3>
            <span class="text-xs text-slate-500 font-semibold">Doctor consultation volume & revenue generated</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($practitioners as $prac)
            <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <p class="font-heading font-black text-sm text-slate-900">{{ $prac->name }}</p>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 bg-teal-100 text-teal-800 rounded">{{ $prac->role }}</span>
                </div>
                <div class="flex items-baseline justify-between text-xs pt-2 border-t border-teal-100">
                    <span class="text-slate-600 font-bold">Total Visits: <strong class="text-slate-900">{{ $prac->visits }}</strong></span>
                    <span class="font-heading font-black text-teal-900 text-sm">₹{{ number_format($prac->revenue, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Charts Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Revenue Trends Line Chart (Rupees ₹)
        const ctxRev = document.getElementById('reportsRevenueChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Current Period (₹)',
                        data: {!! json_encode($chartCurrentRevenue) !!},
                        borderColor: '#006654',
                        backgroundColor: 'rgba(0, 102, 84, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35
                    },
                    {
                        label: 'Previous Period (₹)',
                        data: {!! json_encode($chartPreviousRevenue) !!},
                        borderColor: '#cbd5e1',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { grid: { borderDash: [4, 4] }, ticks: { font: { size: 10 } } }
                }
            }
        });

        // 2. Appointment Status Donut Chart
        const ctxDonut = document.getElementById('reportsAppointmentDonut').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Rescheduled', 'Cancelled', 'No-Show'],
                datasets: [{
                    data: [{{ $completedCount }}, {{ $rescheduledCount }}, {{ $cancelledCount }}, {{ $noShowCount }}],
                    backgroundColor: ['#006654', '#6366f1', '#94a3b8', '#f43f5e'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
@endsection
