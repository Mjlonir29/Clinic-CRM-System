@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedPatient: {
        name: '{{ $appointments->first()->patient->full_name ?? "Marcus Jenkins" }}',
        mrn: '{{ $appointments->first()->patient->patient_id ?? "PT-8092" }}',
        age: {{ $appointments->first()->patient->age ?? 45 }},
        gender: '{{ $appointments->first()->patient->gender ?? "Male" }}',
        phone: '{{ $appointments->first()->patient->phone ?? "+91 98200 12345" }}',
        blood_group: '{{ $appointments->first()->patient->blood_group ?? "O+" }}',
        bp: '138/88',
        hr: '74 bpm',
        temp: '98.6 °F',
        spo2: '99%',
        allergies: ['Penicillin', 'Sulfa Drugs'],
        complaints: ['{{ $appointments->first()->reason_for_visit ?? "Hypertension follow-up & chronic headache" }}'],
        notes: 'Patient checked in at reception counter. Nurse triage telemetry completed.',
        appointment_id: {{ $appointments->first()->id ?? 1 }},
        patient_id: {{ $appointments->first()->patient_id ?? 1 }}
    },
    createModalOpen: false 
}">

    <!-- Header Breadcrumbs & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinical Administration</span>
                <span>/</span>
                <span class="text-teal-800">Appointment Scheduling</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Appointments & Consultation Queue</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Manage patient check-ins, triage telemetry, and direct EHR consultation entry</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('appointments.calendar') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center border border-slate-200">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Calendar View
            </a>
            <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/20 transition-all flex items-center transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Schedule Appointment
            </a>
        </div>
    </div>

    <!-- Triage Status Summary Pills Bar -->
    <div class="flex flex-wrap items-center gap-3 bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs text-xs font-extrabold">
        <span class="text-slate-400 uppercase text-[10px] tracking-wider font-black mr-1">Triage Summary:</span>
        <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-800 border border-slate-200/60">{{ $totalToday }} Total Today</span>
        <span class="px-3 py-1 rounded-xl bg-blue-50 text-blue-800 border border-blue-200/60">{{ $waitingToday }} In Waiting Room</span>
        <span class="px-3 py-1 rounded-xl bg-purple-50 text-purple-800 border border-purple-200/60">{{ $consultingToday }} In Consultation</span>
        <span class="px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200/60">{{ $completedToday }} Completed</span>
    </div>

    <!-- Main Content Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Timeline Grid (2 Columns) -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Filter Toolbar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-card flex flex-col sm:flex-row items-center justify-between gap-3">
                <form method="GET" action="{{ route('appointments.index') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search patient or appt #..." 
                        class="px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 w-full sm:w-52 font-medium"
                    />
                    
                    <select name="status" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Confirmed" {{ request('status') === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="Checked In" {{ request('status') === 'Checked In' ? 'selected' : '' }}>Checked In</option>
                        <option value="In Consultation" {{ request('status') === 'In Consultation' ? 'selected' : '' }}>In Consultation</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium" />

                    <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition-all">Filter</button>
                    @if(request()->hasAny(['search', 'status', 'date']))
                        <a href="{{ route('appointments.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Appointments Cards Timeline -->
            <div class="space-y-3">
                @forelse($appointments as $apt)
                    <div 
                        @click="selectedPatient = {
                            name: '{{ addslashes($apt->patient->full_name ?? '') }}',
                            mrn: '{{ $apt->patient->patient_id ?? 'PT-8092' }}',
                            age: {{ $apt->patient->age ?? 45 }},
                            gender: '{{ $apt->patient->gender ?? 'Male' }}',
                            phone: '{{ $apt->patient->phone ?? '' }}',
                            blood_group: '{{ $apt->patient->blood_group ?? 'O+' }}',
                            bp: '138/88',
                            hr: '74 bpm',
                            temp: '98.6 °F',
                            spo2: '99%',
                            allergies: ['Penicillin', 'Sulfa Drugs'],
                            complaints: ['{{ addslashes($apt->reason_for_visit ?? 'Routine Consultation') }}'],
                            notes: 'Patient checked in at reception counter. Nurse triage telemetry completed.',
                            appointment_id: {{ $apt->id }},
                            patient_id: {{ $apt->patient_id }}
                        }"
                        class="p-4 sm:p-5 rounded-3xl bg-white border border-slate-200/90 shadow-card hover:border-teal-300 transition-all cursor-pointer group relative overflow-hidden"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            <!-- Left Details -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 rounded-2xl bg-teal-800 text-white font-extrabold flex items-center justify-center text-sm flex-shrink-0 shadow-sm">
                                    {{ strtoupper(substr($apt->patient->first_name ?? 'P', 0, 1) . substr($apt->patient->last_name ?? '', 0, 1)) }}
                                </div>

                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h3 class="font-heading font-black text-slate-900 text-sm group-hover:text-teal-800 transition-colors">
                                            {{ $apt->patient->full_name }}
                                        </h3>
                                        <span class="text-[10px] font-extrabold text-slate-400">#{{ $apt->patient->patient_id }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-teal-50 text-teal-800 border border-teal-200/60">
                                            {{ $apt->appointment_type }}
                                        </span>
                                    </div>

                                    <div class="flex items-center space-x-2 text-xs text-slate-500 font-medium mt-1">
                                        <span>⏰ {{ date('h:i A', strtotime($apt->appointment_time)) }}</span>
                                        <span>•</span>
                                        <span>Dr. {{ $apt->doctor->name ?? 'Marcus Vance' }}</span>
                                        <span>•</span>
                                        <span class="text-rose-700 font-extrabold">BP: 140/90 (High)</span>
                                    </div>

                                    @if($apt->reason_for_visit)
                                        <p class="text-xs text-slate-500 italic mt-1 truncate max-w-md">"{{ $apt->reason_for_visit }}"</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Status & Action -->
                            <div class="flex items-center space-x-3 self-end sm:self-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black
                                    @if($apt->status === 'Completed') bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @elseif($apt->status === 'In Consultation') bg-purple-100 text-purple-800 border border-purple-200
                                    @elseif($apt->status === 'Checked In') bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif($apt->status === 'Confirmed') bg-teal-100 text-teal-800 border border-teal-200
                                    @else bg-amber-100 text-amber-800 border border-amber-200 @endif
                                ">
                                    {{ $apt->status }}
                                </span>

                                <a href="{{ route('prescriptions.create', ['appointment_id' => $apt->id, 'patient_id' => $apt->patient_id]) }}" class="px-3.5 py-1.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-2xs transition-all">
                                    Start Rx →
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-3xl border border-slate-200 text-center text-slate-400 font-medium">
                        No appointments found matching your filter criteria.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $appointments->links() }}
            </div>
        </div>

        <!-- Right Patient Telemetry & Details Panel (Image 3 Style) -->
        <div class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sticky top-20 space-y-6">
                
                <!-- Panel Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Patient Triage & EHR Telemetry</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200" x-text="'MRN #' + selectedPatient.mrn"></span>
                </div>

                <!-- Patient Profile Card -->
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 rounded-2xl bg-teal-800 text-white font-heading font-black text-lg flex items-center justify-center shadow-md">
                        <span x-text="selectedPatient.name.split(' ').map(n => n[0]).join('')"></span>
                    </div>
                    <div>
                        <h2 class="font-heading font-black text-lg text-slate-900" x-text="selectedPatient.name"></h2>
                        <p class="text-xs font-semibold text-slate-500" x-text="selectedPatient.age + ' yrs • ' + selectedPatient.gender + ' • Blood Group: ' + selectedPatient.blood_group"></p>
                        <p class="text-xs font-medium text-teal-800 mt-0.5" x-text="selectedPatient.phone"></p>
                    </div>
                </div>

                <!-- Physical Vitals Mini Grid -->
                <div class="space-y-2">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Physical Vitals Telemetry</h3>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200/60">
                            <span class="text-[10px] font-extrabold text-amber-800 uppercase block">Blood Pressure</span>
                            <span class="text-base font-heading font-black text-slate-900" x-text="selectedPatient.bp"></span>
                            <span class="text-[9px] font-extrabold text-amber-800 block mt-0.5">Stage 1 HTN</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-emerald-50/70 border border-emerald-200/60">
                            <span class="text-[10px] font-extrabold text-emerald-800 uppercase block">Heart Rate</span>
                            <span class="text-base font-heading font-black text-slate-900" x-text="selectedPatient.hr"></span>
                            <span class="text-[9px] font-extrabold text-emerald-800 block mt-0.5">Normal Sinus</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Body Temp</span>
                            <span class="text-base font-heading font-black text-slate-900" x-text="selectedPatient.temp"></span>
                            <span class="text-[9px] font-bold text-slate-500 block mt-0.5">Afebrile</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase block">SpO2 Level</span>
                            <span class="text-base font-heading font-black text-slate-900" x-text="selectedPatient.spo2"></span>
                            <span class="text-[9px] font-bold text-slate-500 block mt-0.5">Room Air</span>
                        </div>
                    </div>
                </div>

                <!-- Chief Complaints & Allergy Pills -->
                <div class="space-y-3">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">Chief Complaints</h4>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="c in selectedPatient.complaints" :key="c">
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-extrabold border border-slate-200" x-text="c"></span>
                            </template>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-extrabold text-rose-800 uppercase tracking-wider mb-1.5">Known Drug Allergies</h4>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="a in selectedPatient.allergies" :key="a">
                                <span class="px-2.5 py-1 rounded-xl bg-rose-50 text-rose-800 text-[11px] font-black border border-rose-200" x-text="'⚠️ ' + a"></span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Nurse Triage Notes -->
                <div class="space-y-1.5">
                    <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Nurse Triage Notes</h4>
                    <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-200/80 leading-relaxed italic" x-text="selectedPatient.notes"></p>
                </div>

                <!-- Consultation CTA Button -->
                <a 
                    :href="'/prescriptions/create?appointment_id=' + selectedPatient.appointment_id + '&patient_id=' + selectedPatient.patient_id"
                    class="w-full flex items-center justify-center py-3.5 px-4 bg-teal-800 hover:bg-teal-900 text-white font-heading font-extrabold text-xs rounded-2xl shadow-lg shadow-teal-900/25 transition-all transform hover:-translate-y-0.5"
                >
                    Start Consultation & Prescription (Rx) →
                </a>

            </div>
        </div>

    </div>

    <!-- Create Appointment Modal -->
    <div x-show="createModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="createModalOpen = false"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-heading font-extrabold text-slate-900">Book New Appointment</h3>
                    <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('appointments.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Select Patient *</label>
                        <select name="patient_id" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium">
                            <option value="">Choose Patient...</option>
                            @foreach(\App\Models\Patient::orderBy('first_name')->get() as $p)
                                <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->patient_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Date *</label>
                            <input type="date" name="appointment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium" />
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Time Slot *</label>
                            <input type="time" name="appointment_time" value="09:30" required class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Appointment Type *</label>
                        <select name="appointment_type" required class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium">
                            <option value="General Consultation">General Consultation</option>
                            <option value="Follow-up">Follow-up Visit</option>
                            <option value="Routine Checkup">Routine Checkup</option>
                            <option value="Emergency">Emergency Triage</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Reason for Visit</label>
                        <textarea name="reason_for_visit" rows="2" placeholder="Patient's primary complaint..." class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/20">Create Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
