@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: 'all'
}">

    <!-- Top Greeting Banner & Live Status Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-heading font-extrabold text-slate-900 tracking-tight">Good morning, {{ auth()->user()->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1.5"></span>
                    Ekta CRM Active
                </span>
            </div>
            <p class="text-xs font-semibold text-slate-500 mt-1">
                Clinical Operations & Patient Telemetry Overview • <span class="text-slate-800 font-bold">{{ now()->format('l, F j, Y') }}</span>
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Add Logo from Settings Button -->
            <a href="{{ route('settings.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-xl transition-all flex items-center border border-slate-200">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Upload Clinic Logo
            </a>
            <a href="{{ route('appointments.create') }}" class="px-4 py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/20 transition-all flex items-center transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Book Appointment
            </a>
        </div>
    </div>

    <!-- 3 Key Statistics Cards Grid (Total Doctors, Today's Appointments, Monthly Revenue) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        <!-- Stat Card 1: Total Doctors -->
        <a href="{{ route('doctors.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card hover:shadow-lg hover:border-teal-300 transition-all block group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider group-hover:text-teal-800">Total Doctors</span>
                <span class="text-[10px] font-black text-teal-800 bg-teal-50 border border-teal-200/60 px-2 py-0.5 rounded-full">Medical Staff</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">{{ \App\Models\User::where('role_slug', 'admin')->count() }}</p>
                <span class="text-xs text-slate-400 font-semibold">Active Doctors</span>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-1">
                    <span>Active Roster</span>
                    <span>100%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-teal-800 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </a>

        <!-- Stat Card 2: Today's Appointments -->
        <a href="{{ route('appointments.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card hover:shadow-lg hover:border-teal-300 transition-all block group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider group-hover:text-teal-800">Today's Appointments</span>
                <span class="text-[10px] font-black text-indigo-700 bg-indigo-50 border border-indigo-200/60 px-2 py-0.5 rounded-full">Scheduled Queue</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-heading font-black text-teal-800 tracking-tight">{{ number_format($todaysAppointmentsCount) }}</p>
                <span class="text-xs text-slate-400 font-semibold">scheduled today</span>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-1">
                    <span>Queue Progress</span>
                    <span>65%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-teal-700 rounded-full" style="width: 65%"></div>
                </div>
            </div>
        </a>

        <!-- Stat Card 3: Monthly Revenue -->
        <a href="{{ route('invoices.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card hover:shadow-lg hover:border-teal-300 transition-all block group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider group-hover:text-teal-800">Monthly Revenue</span>
                <span class="text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded-full">Collected</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">₹{{ number_format($thisMonthRevenue, 2) }}</p>
                <span class="text-xs text-slate-400 font-semibold">INR</span>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-1">
                    <span>Target Target</span>
                    <span>94%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full" style="width: 94%"></div>
                </div>
            </div>
        </a>

    </div>

    <!-- Main Content: Today's Patient Queue Section -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
        <!-- Table Header with Filter Tabs -->
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-heading font-extrabold text-slate-900">Today's Patient Queue</h3>
                <p class="text-xs text-slate-500 font-medium">Real-time patient check-in & consultation workflow for today</p>
            </div>

            <!-- Interactive Filter Pill Tabs -->
            <div class="flex items-center bg-slate-100 p-1 rounded-2xl text-xs font-extrabold space-x-1">
                <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-xl transition-all">All ({{ count($todaysAppointments) }})</button>
                <button @click="activeTab = 'waiting'" :class="activeTab === 'waiting' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-xl transition-all">Waiting</button>
                <button @click="activeTab = 'consultation'" :class="activeTab === 'consultation' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-xl transition-all">In Consult</button>
                <button @click="activeTab = 'completed'" :class="activeTab === 'completed' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-xl transition-all">Done</button>
            </div>
        </div>

        <!-- Today's Appointments Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Time Slot</th>
                        <th class="py-3.5 px-6">Patient Details</th>
                        <th class="py-3.5 px-6">Reason for Visit</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($todaysAppointments as $apt)
                        <tr 
                            x-show="
                                activeTab === 'all' ||
                                (activeTab === 'waiting' && ['Pending', 'Confirmed', 'Checked In'].includes('{{ $apt->status }}')) ||
                                (activeTab === 'consultation' && '{{ $apt->status }}' === 'In Consultation') ||
                                (activeTab === 'completed' && '{{ $apt->status }}' === 'Completed')
                            "
                            class="hover:bg-teal-50/30 transition-colors"
                        >
                            <td class="py-4 px-6">
                                <div class="font-extrabold text-slate-900">{{ date('h:i A', strtotime($apt->appointment_time)) }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase">Slot #0{{ $loop->iteration }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 font-extrabold flex items-center justify-center text-xs flex-shrink-0">
                                        {{ strtoupper(substr($apt->patient->first_name ?? 'P', 0, 1) . substr($apt->patient->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('patients.show', $apt->patient_id) }}" class="font-heading font-extrabold text-slate-900 hover:text-teal-800 transition-colors block">
                                            {{ $apt->patient->full_name }}
                                        </a>
                                        <div class="flex items-center space-x-2 text-[10px] text-slate-500 font-medium">
                                            <span class="font-bold text-teal-800">{{ $apt->patient->patient_id }}</span>
                                            <span>•</span>
                                            <span>{{ $apt->patient->phone ?? '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <p class="text-xs font-semibold text-slate-800 max-w-xs truncate">
                                    {{ $apt->reason_for_visit ?? 'General Consultation & Routine Checkup' }}
                                </p>
                            </td>

                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black
                                    @if($apt->status === 'Completed') bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @elseif($apt->status === 'In Consultation') bg-purple-100 text-purple-800 border border-purple-200
                                    @elseif($apt->status === 'Checked In') bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif($apt->status === 'Confirmed') bg-teal-100 text-teal-800 border border-teal-200
                                    @elseif($apt->status === 'Cancelled') bg-rose-100 text-rose-800 border border-rose-200
                                    @else bg-amber-100 text-amber-800 border border-amber-200 @endif
                                ">
                                    {{ $apt->status }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('prescriptions.create', ['appointment_id' => $apt->id, 'patient_id' => $apt->patient_id]) }}" class="px-3 py-1.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-[11px] rounded-xl shadow-2xs transition-all">
                                        Start Rx
                                    </a>
                                    <a href="{{ route('appointments.show', $apt->id) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] rounded-xl transition-all">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-medium">
                                No patient queue for today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-bold text-slate-500">
            <span>Showing Today's Patient Queue ({{ count($todaysAppointments) }} appointments)</span>
            <a href="{{ route('appointments.index') }}" class="text-teal-800 hover:text-teal-900 underline">View Appointments Calendar →</a>
        </div>
    </div>

</div>
@endsection
