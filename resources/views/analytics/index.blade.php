@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Executive Intelligence</span>
                <span>/</span>
                <span class="text-teal-800">Advanced Analytics & Business Intelligence</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Analytics & Clinical Intelligence</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Revenue breakdown, top services, doctor performance matrix, and patient retention analytics.</p>
        </div>
    </div>

    <!-- 4 Dynamic KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Total Patient Base</span>
            <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">{{ $totalPatients }}</p>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-3 pt-2 border-t">
                <span class="text-emerald-700 font-extrabold">{{ $newPatients }} New Patients</span>
                <span>{{ $returningPatients }} Returning</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Patient Retention Rate</span>
            <p class="text-3xl font-heading font-black text-teal-800 tracking-tight">{{ $retentionRate }}%</p>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-3 pt-2 border-t">
                <span class="text-emerald-700 font-extrabold">📈 Active Retention</span>
                <span>High Engagement</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Follow-up Compliance Rate</span>
            <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">{{ $followupComplianceRate }}%</p>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-3 pt-2 border-t">
                <span class="text-teal-700 font-extrabold">Completed Visits</span>
                <span>Low Drop-off</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Total Revenue Growth</span>
            <p class="text-3xl font-heading font-black text-emerald-700 tracking-tight">₹{{ number_format(array_sum($revenueData), 2) }}</p>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-3 pt-2 border-t">
                <span class="text-emerald-700 font-extrabold">6 Month Total</span>
                <span>Verified Cash Flow</span>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Monthly Revenue & Peak Hours -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Revenue Chart -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-black text-slate-900 text-sm">Monthly Revenue Breakdown & Growth Trend</h3>
                    <p class="text-xs text-slate-400 font-semibold">Cash flow collections (in ₹) over the last 6 months</p>
                </div>
                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 text-[10px] font-black rounded border border-emerald-200">Revenue INR</span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top Clinical Services Billed -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-4">
            <h3 class="font-heading font-black text-slate-900 text-sm">Top Clinical Services Billed</h3>
            <p class="text-xs text-slate-400 font-semibold">Highest revenue-generating medical services</p>
            
            <div class="space-y-3 pt-2">
                @forelse($topServices as $srv)
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-extrabold text-slate-900 block">{{ $srv->service_name }}</span>
                            <span class="text-[10px] text-slate-400 font-bold">{{ $srv->total_count }} procedures</span>
                        </div>
                        <span class="font-heading font-black text-teal-800 text-sm">₹{{ number_format($srv->total_revenue, 2) }}</span>
                    </div>
                @empty
                    <div class="text-slate-400 text-xs italic text-center py-8">No billing records found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Doctor Performance Matrix -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-heading font-black text-slate-900 text-sm">Doctor Performance Matrix & Rating Summary</h3>
                <p class="text-xs text-slate-400 font-semibold">Completed consultations, generated revenue, and patient satisfaction ratings</p>
            </div>
            <span class="px-3 py-1 bg-teal-50 text-teal-800 text-[10px] font-black rounded-md border border-teal-200 uppercase">Live Performance</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="p-3.5">Doctor Name</th>
                        <th class="p-3.5">Specialization</th>
                        <th class="p-3.5 text-center">Consultations Completed</th>
                        <th class="p-3.5 text-right">Revenue Generated (₹)</th>
                        <th class="p-3.5 text-right">Patient Satisfaction Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($doctorStats as $d)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3.5 font-bold text-slate-900">{{ $d['doctor']->name }}</td>
                            <td class="p-3.5 text-slate-600 font-semibold">{{ $d['doctor']->specialization ?? 'General Physician' }}</td>
                            <td class="p-3.5 text-center font-bold text-slate-900">{{ $d['consultations_count'] }}</td>
                            <td class="p-3.5 text-right font-heading font-black text-teal-800 text-sm">₹{{ number_format($d['revenue_generated'], 2) }}</td>
                            <td class="p-3.5 text-right">
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-black border border-amber-200 text-xs">
                                    <span>⭐ {{ $d['rating'] }}</span>
                                    <span class="text-[10px] text-amber-600 font-semibold">/ 5.0</span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js Setup -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($revenueMonths),
                datasets: [{
                    label: 'Revenue Collections (₹)',
                    data: @json($revenueData),
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#006654',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return '₹' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
