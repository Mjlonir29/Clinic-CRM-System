@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header & View Switchers -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Appointment Calendar</h1>
            <p class="text-xs font-semibold text-slate-500 mt-1">Interactive Day, Week, and Month scheduling timeline</p>
        </div>
        <div class="flex items-center space-x-2 bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
            <a href="{{ route('appointments.calendar', ['view' => 'day', 'date' => $date]) }}" class="px-4 py-1.5 text-xs font-bold rounded-xl transition-all {{ $view === 'day' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Day
            </a>
            <a href="{{ route('appointments.calendar', ['view' => 'week', 'date' => $date]) }}" class="px-4 py-1.5 text-xs font-bold rounded-xl transition-all {{ $view === 'week' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Week
            </a>
            <a href="{{ route('appointments.calendar', ['view' => 'month', 'date' => $date]) }}" class="px-4 py-1.5 text-xs font-bold rounded-xl transition-all {{ $view === 'month' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Month
            </a>
            <a href="{{ route('appointments.index') }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-teal-700 border-l border-slate-200 pl-3">
                List View →
            </a>
        </div>
    </div>

    <!-- Date Navigation Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('appointments.calendar', ['view' => $view, 'date' => $carbonDate->copy()->subMonth()->format('Y-m-d')]) }}" class="p-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold rounded-xl transition-all text-xs">
                ← Prev
            </a>
            <h2 class="text-base font-extrabold text-slate-900">
                {{ $carbonDate->format('F Y') }}
            </h2>
            <a href="{{ route('appointments.calendar', ['view' => $view, 'date' => $carbonDate->copy()->addMonth()->format('Y-m-d')]) }}" class="p-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold rounded-xl transition-all text-xs">
                Next →
            </a>
        </div>
        <a href="{{ route('appointments.calendar', ['view' => $view, 'date' => date('Y-m-d')]) }}" class="px-3.5 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 text-xs font-bold rounded-xl border border-teal-200">
            Today
        </a>
    </div>

    <!-- Month Grid View -->
    @if($view === 'month')
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden p-4 sm:p-6">
            <div class="grid grid-cols-7 gap-2 mb-2 text-center text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
            </div>

            <div class="grid grid-cols-7 gap-2 text-xs">
                @php
                    $firstDayOfMonth = $carbonDate->copy()->startOfMonth();
                    $daysInMonth = $carbonDate->daysInMonth;
                    $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 for Sun
                @endphp

                <!-- Blank offset days -->
                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div class="h-28 bg-slate-50/50 rounded-2xl border border-slate-100/50 opacity-40"></div>
                @endfor

                <!-- Month Days -->
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDateStr = $carbonDate->copy()->day($day)->format('Y-m-d');
                        $dayAppointments = $appointments->where('appointment_date', $currentDateStr);
                        $isToday = $currentDateStr === date('Y-m-d');
                    @endphp
                    <div class="h-28 p-2 rounded-2xl border flex flex-col justify-between transition-all {{ $isToday ? 'bg-teal-50/60 border-teal-300 ring-2 ring-teal-500/20' : 'bg-white border-slate-100 hover:border-slate-200' }}">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold {{ $isToday ? 'text-teal-700' : 'text-slate-800' }}">{{ $day }}</span>
                            @if(count($dayAppointments) > 0)
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ count($dayAppointments) }}
                                </span>
                            @endif
                        </div>

                        <div class="space-y-1 overflow-y-auto max-h-16 pr-1">
                            @foreach($dayAppointments as $apt)
                                <a href="{{ route('patients.show', $apt->patient_id) }}" class="block px-1.5 py-0.5 rounded text-[10px] font-semibold truncate
                                    @if($apt->status === 'Completed') bg-emerald-100 text-emerald-800
                                    @elseif($apt->status === 'In Consultation') bg-purple-100 text-purple-800
                                    @elseif($apt->status === 'Checked In') bg-blue-100 text-blue-800
                                    @else bg-amber-100 text-amber-800 @endif
                                " title="{{ $apt->patient->full_name }} ({{ $apt->appointment_time }})">
                                    {{ $apt->appointment_time }} {{ $apt->patient->first_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @else
        <!-- Day / Week Timeline View -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 divide-y divide-slate-100">
            @forelse($appointments as $apt)
                <div class="py-4 flex items-center justify-between hover:bg-slate-50 px-4 rounded-2xl transition-all">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 text-center">
                            <span class="font-extrabold text-teal-700 text-sm block">{{ $apt->appointment_time }}</span>
                            <span class="text-[10px] text-slate-400 font-semibold">{{ $apt->appointment_date }}</span>
                        </div>
                        <div>
                            <a href="{{ route('patients.show', $apt->patient_id) }}" class="font-bold text-slate-900 text-sm hover:text-teal-600">
                                {{ $apt->patient->full_name }}
                            </a>
                            <p class="text-xs text-slate-500 mt-0.5">Type: <span class="font-semibold text-slate-700">{{ $apt->appointment_type }}</span> • {{ $apt->patient->phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                            {{ $apt->status }}
                        </span>
                        <a href="{{ route('consultations.create', ['appointment_id' => $apt->id]) }}" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-sm">
                            Consult →
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-medium">
                    No appointments found for this selected calendar view.
                </div>
            @endforelse
        </div>
    @endif
</div>
@endsection
