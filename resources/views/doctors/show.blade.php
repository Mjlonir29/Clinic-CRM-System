@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header Navigation & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('doctors.index') }}" class="hover:text-teal-800 transition-colors">Doctors Directory</a>
                <span>/</span>
                <span class="text-teal-800">Doctor Profile & Medical Qualifications</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">{{ $doctor->name }}</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Medical Specialist Credentials, Clinical History & OPD Patient Appointments</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('doctors.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all border border-slate-200 flex items-center">
                ← Back to Doctors Roster
            </a>
            <a href="{{ route('appointments.create') }}?doctor_id={{ $doctor->id }}" class="px-4 py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-teal-900/20 transition-all flex items-center">
                + Book Appointment
            </a>
        </div>
    </div>

    <!-- Main Doctor Credentials Banner Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sm:p-8 space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between border-b border-slate-100 pb-6 gap-6">
            <div class="flex items-start sm:items-center space-x-5">
                <div class="w-20 h-20 rounded-2xl bg-teal-800 text-white font-heading font-black flex items-center justify-center text-3xl shadow-lg flex-shrink-0">
                    {{ strtoupper(substr($doctor->name, 0, 1)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center space-x-3">
                        <h2 class="text-2xl font-heading font-black text-slate-900">{{ $doctor->name }}</h2>
                        <span class="px-3 py-1 rounded-lg text-xs font-black bg-teal-50 text-teal-800 border border-teal-200">
                            {{ $doctor->doctor_id }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase">
                            Active Practitioner
                        </span>
                    </div>
                    <p class="text-sm font-extrabold text-teal-800 flex items-center">
                        <span class="mr-1.5">🎓</span>
                        <span>{{ $doctor->qualifications ?? 'MBBS, MD (General Medicine)' }}</span>
                    </p>
                    <p class="text-xs font-bold text-slate-600 flex items-center">
                        <span class="mr-1.5">🏥</span>
                        <span>Department: {{ $doctor->specialization ?? 'General Medicine & OPD Services' }}</span>
                    </p>
                </div>
            </div>

            <div class="bg-teal-50/70 border border-teal-100 rounded-2xl p-4 lg:text-right flex lg:flex-col justify-between items-center lg:items-end">
                <div>
                    <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider block">OPD Consultation Fee</span>
                    <span class="text-3xl font-black text-teal-900">₹{{ number_format($doctor->consultation_fee ?? 500, 2) }}</span>
                </div>
                <span class="text-[10px] font-bold text-teal-600 mt-1">per consultation session</span>
            </div>
        </div>

        <!-- Qualifications & Medical Metadata Grid -->
        <div>
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-3">Official Qualifications & Medical Registry</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 p-5 bg-slate-50/80 rounded-2xl border border-slate-100 text-xs">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-1">MCI / NMC Reg. Number</span>
                    <span class="font-mono font-bold text-slate-900 bg-white px-2.5 py-1 rounded-lg border border-slate-200 block text-center sm:text-left">{{ $doctor->registration_number ?? 'MCI-2024-98765' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-1">Total Experience</span>
                    <span class="font-extrabold text-slate-900 bg-white px-2.5 py-1 rounded-lg border border-slate-200 block text-center sm:text-left">{{ $doctor->experience_years ?? '15+ Years' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-1">Clinic Cabin / OPD Room</span>
                    <span class="font-bold text-slate-900 bg-white px-2.5 py-1 rounded-lg border border-slate-200 block text-center sm:text-left">{{ $doctor->cabin_number ?? 'OPD Cabin 3B' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-1">Available Shift Hours</span>
                    <span class="font-bold text-slate-900 bg-white px-2.5 py-1 rounded-lg border border-slate-200 block text-center sm:text-left">{{ $doctor->working_hours ?? 'Mon-Sat: 9:00 AM - 5:00 PM' }}</span>
                </div>
            </div>
        </div>

        <!-- Contact & Metrics Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs">
            <div class="flex items-center space-x-3 p-3.5 rounded-2xl bg-white border border-slate-100 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-black">📧</div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Email Address</span>
                    <span class="font-extrabold text-slate-900">{{ $doctor->email }}</span>
                </div>
            </div>

            <div class="flex items-center space-x-3 p-3.5 rounded-2xl bg-white border border-slate-100 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-black">📞</div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Contact Phone</span>
                    <span class="font-extrabold text-slate-900">{{ $doctor->phone ?? '+91 98765 43210' }}</span>
                </div>
            </div>

            <div class="flex items-center space-x-3 p-3.5 rounded-2xl bg-teal-50/60 border border-teal-100 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-teal-800 text-white flex items-center justify-center font-black">📋</div>
                <div>
                    <span class="text-[10px] font-bold text-teal-700 uppercase block">Patient Visits Completed</span>
                    <span class="font-black text-teal-900 text-base">{{ $doctor->visit_count ?? 0 }} Total Visits</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctor's Scheduled Appointments & Visit Log -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-lg font-heading font-black text-slate-900">Doctor Patient Appointments & Visit History</h3>
                <p class="text-xs text-slate-500 font-semibold">List of recent patient appointments scheduled under {{ $doctor->name }}</p>
            </div>
            <a href="{{ route('appointments.create') }}?doctor_id={{ $doctor->id }}" class="text-xs font-extrabold text-teal-800 hover:underline">
                + Schedule New Visit
            </a>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-100">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-black tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Appointment Code</th>
                        <th class="px-5 py-3.5">Date & Time</th>
                        <th class="px-5 py-3.5">Patient Details</th>
                        <th class="px-5 py-3.5">Reason for Visit</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-teal-50/40 transition-colors">
                        <td class="px-5 py-4 font-mono font-bold text-slate-600">
                            {{ $appointment->appointment_number ?? 'APT-' . $appointment->id }}
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }} at {{ $appointment->appointment_time }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-900">{{ $appointment->patient->full_name ?? 'N/A' }}</span>
                            @if(isset($appointment->patient->phone))
                                <span class="block text-[10px] text-slate-400 font-medium">📞 {{ $appointment->patient->phone }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-700 font-semibold">
                            {{ $appointment->reason_for_visit ?? 'General OPD Consultation' }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase border 
                                @if($appointment->status === 'completed') bg-emerald-50 text-emerald-800 border-emerald-200
                                @elseif($appointment->status === 'confirmed') bg-teal-50 text-teal-800 border-teal-200
                                @elseif($appointment->status === 'cancelled') bg-rose-50 text-rose-800 border-rose-200
                                @else bg-slate-100 text-slate-700 border-slate-200
                                @endif">
                                {{ $appointment->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400 font-medium">No appointments scheduled for this doctor yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
        <div class="pt-4">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
