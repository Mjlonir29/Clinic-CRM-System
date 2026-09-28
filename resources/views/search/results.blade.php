@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Global Search Results</h1>
        <p class="text-xs font-semibold text-slate-500 mt-1">Showing search matches for: <strong class="text-teal-700">"{{ $query }}"</strong></p>
    </div>

    <!-- Patients Results -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-3">
        <h3 class="text-sm font-extrabold text-slate-900 border-b pb-2">Matching Patients ({{ count($patients) }})</h3>
        <div class="divide-y divide-slate-100 text-xs">
            @forelse($patients as $p)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <a href="{{ route('patients.show', $p->id) }}" class="font-extrabold text-slate-900 text-sm hover:text-teal-600">{{ $p->full_name }}</a>
                        <span class="text-slate-400 text-xs ml-2">({{ $p->patient_id }})</span>
                        <p class="text-slate-500 mt-0.5">Phone: {{ $p->phone }} • Email: {{ $p->email ?? 'N/A' }}</p>
                    </div>
                    <a href="{{ route('patients.show', $p->id) }}" class="px-3 py-1.5 bg-teal-50 text-teal-700 font-bold rounded-xl">View Profile →</a>
                </div>
            @empty
                <p class="text-slate-400 text-xs py-2">No matching patient records found.</p>
            @endforelse
        </div>
    </div>

    <!-- Appointments Results -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-3">
        <h3 class="text-sm font-extrabold text-slate-900 border-b pb-2">Matching Appointments ({{ count($appointments) }})</h3>
        <div class="divide-y divide-slate-100 text-xs">
            @forelse($appointments as $apt)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <span class="font-extrabold text-teal-700">{{ $apt->appointment_number }}</span>
                        <span class="text-slate-800 font-bold ml-2">{{ $apt->patient->full_name ?? 'N/A' }}</span>
                        <p class="text-slate-500 mt-0.5">{{ $apt->appointment_date }} at {{ $apt->appointment_time }} • Status: <strong>{{ $apt->status }}</strong></p>
                    </div>
                    <a href="{{ route('consultations.create', ['appointment_id' => $apt->id]) }}" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-xl">Open Appointment →</a>
                </div>
            @empty
                <p class="text-slate-400 text-xs py-2">No matching appointment records found.</p>
            @endforelse
        </div>
    </div>

    <!-- Invoices Results -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-3">
        <h3 class="text-sm font-extrabold text-slate-900 border-b pb-2">Matching Invoices ({{ count($invoices) }})</h3>
        <div class="divide-y divide-slate-100 text-xs">
            @forelse($invoices as $inv)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <span class="font-extrabold text-teal-700">{{ $inv->invoice_number }}</span>
                        <span class="text-slate-800 font-bold ml-2">{{ $inv->patient->full_name ?? 'N/A' }}</span>
                        <p class="text-slate-500 mt-0.5">Date: {{ $inv->invoice_date }} • Total: <strong>${{ number_format($inv->total_amount, 2) }}</strong> • Status: {{ $inv->status }}</p>
                    </div>
                    <a href="{{ route('invoices.show', $inv->id) }}" class="px-3 py-1.5 bg-teal-50 text-teal-700 font-bold rounded-xl">View Invoice →</a>
                </div>
            @empty
                <p class="text-slate-400 text-xs py-2">No matching invoices found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
