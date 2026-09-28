@extends('layouts.app')

@section('content')
<div class="space-y-8">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">
                <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Medical Staff & Clinical Roster</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">Doctors Directory & Attendance</h1>
            <p class="text-xs text-slate-500 font-semibold mt-1">Manage doctor profiles, unique Doctor IDs, degrees, MCI numbers, and track doctor patient visits by date.</p>
        </div>
        <a href="{{ route('doctors.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-800 hover:bg-teal-900 active:bg-teal-950 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-teal-900/20 transition-all transform hover:-translate-y-0.5 self-start sm:self-auto">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Add New Doctor</span>
        </a>
    </div>

    <!-- Doctors Cards Roster -->
    <div class="space-y-4">
        <h2 class="text-lg font-heading font-extrabold text-slate-900">Active Doctors Roster & Qualifications</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($doctors as $doctor)
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card hover:border-teal-400 hover:shadow-lg transition-all flex flex-col justify-between space-y-4 group">
                <div class="flex items-start justify-between">
                    <a href="{{ route('doctors.show', $doctor->id) }}" class="flex items-center space-x-4 group-hover:opacity-95">
                        <div class="w-14 h-14 rounded-2xl bg-teal-800 text-white font-heading font-black flex items-center justify-center text-lg shadow-md flex-shrink-0 group-hover:bg-teal-900 transition-colors">
                            {{ strtoupper(substr($doctor->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="font-heading font-black text-slate-900 text-lg group-hover:text-teal-800 transition-colors underline-offset-2 hover:underline">{{ $doctor->name }}</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-teal-50 text-teal-800 border border-teal-200">
                                    {{ $doctor->doctor_id }}
                                </span>
                            </div>
                            <p class="text-xs font-extrabold text-teal-800 mt-0.5">🎓 {{ $doctor->qualifications ?? 'MBBS, MD (General Medicine)' }}</p>
                            <p class="text-xs font-semibold text-slate-500">🏥 {{ $doctor->specialization ?? 'General Medicine' }}</p>
                        </div>
                    </a>
                </div>

                <!-- Comprehensive Doctor Qualifications & Details -->
                <a href="{{ route('doctors.show', $doctor->id) }}" class="block grid grid-cols-2 sm:grid-cols-3 gap-3 p-3.5 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-teal-50/50 transition-colors text-xs font-semibold">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Reg. Number (MCI)</span>
                        <span class="font-mono font-bold text-slate-900">{{ $doctor->registration_number ?? 'MCI-2024-98765' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Experience</span>
                        <span class="font-bold text-slate-900">{{ $doctor->experience_years ?? '15 Years' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Consultation Fee</span>
                        <span class="font-extrabold text-teal-800">₹{{ number_format($doctor->consultation_fee ?? 500, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Cabin Room</span>
                        <span class="font-bold text-slate-800">{{ $doctor->cabin_number ?? 'OPD Cabin 3B' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Available Shift Hours</span>
                        <span class="font-bold text-slate-800">{{ $doctor->working_hours ?? 'Mon-Sat: 9:00 AM - 5:00 PM' }}</span>
                    </div>
                </a>

                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center space-x-3 text-slate-500 font-medium">
                        <span>📧 {{ $doctor->email }}</span>
                        <span>•</span>
                        <span>📞 {{ $doctor->phone ?? '+91 98765 43210' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 bg-teal-50 text-teal-900 font-extrabold rounded-xl border border-teal-200">
                            {{ $doctor->visit_count }} Total Visits
                        </span>
                        <a href="{{ route('doctors.show', $doctor->id) }}" class="px-3.5 py-1.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md transition-all flex items-center space-x-1">
                            <span>View Details & Degrees</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Doctor Visits by Date Section -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-heading font-extrabold text-slate-900">Doctor Patient Visit History by Date</h2>
                <p class="text-xs text-slate-500 font-semibold">Track which date how many doctors conducted patient visits.</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-100">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-black tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Visit Date</th>
                        <th class="px-5 py-3.5">Doctor Details & Degrees</th>
                        <th class="px-5 py-3.5">Total Patient Visits Conducted</th>
                        <th class="px-5 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                    @forelse($visitsByDate as $visit)
                    <tr class="hover:bg-teal-50/40 transition-colors">
                        <td class="px-5 py-4 font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($visit->appointment_date)->format('d M Y (D)') }}
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('doctors.show', $visit->doctor_id) }}" class="flex items-center space-x-2 group">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-slate-100 text-slate-700 group-hover:bg-teal-100 group-hover:text-teal-800 transition-colors">
                                    DOC-{{ date('Y') }}-{{ str_pad($visit->doctor_id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="font-bold text-slate-900 group-hover:text-teal-800 transition-colors underline-offset-2 group-hover:underline">{{ $visit->doctor->name ?? 'Dr. Staff' }}</span>
                                <span class="text-[10px] font-semibold text-slate-500">({{ $visit->doctor->qualifications ?? 'MBBS, MD' }})</span>
                            </a>
                        </td>
                        <td class="px-5 py-4 font-extrabold text-teal-800">
                            {{ $visit->visit_count }} Visits
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('doctors.show', $visit->doctor_id) }}" class="inline-flex items-center text-teal-800 font-extrabold text-xs hover:underline">
                                View Profile →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-400">No doctor visit history recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
