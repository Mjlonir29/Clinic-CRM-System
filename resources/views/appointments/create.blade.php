@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header Breadcrumbs & Actions -->
    <div class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('appointments.index') }}" class="hover:text-teal-800">Appointments Schedule</a>
                <span>/</span>
                <span class="text-teal-800">Schedule Appointment</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Book Clinical Appointment</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Schedule a new consultation slot, assign attending doctor with qualifications, set triage priority & payment terms.</p>
        </div>

        <a href="{{ route('appointments.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all border border-slate-200 flex items-center">
            ← Back to Queue
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
            @foreach ($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Main Standalone Booking Form -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sm:p-8">
        <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Patient & Doctor Selection -->
            <div class="space-y-4">
                <h2 class="text-base font-heading font-extrabold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 text-xs flex items-center justify-center mr-2 font-black">1</span>
                    Select Patient & Attending Medical Specialist
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Patient *</label>
                        <select name="patient_id" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                            <option value="">Choose Patient File...</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ (old('patient_id', $selectedPatientId) == $p->id) ? 'selected' : '' }}>
                                    {{ $p->full_name }} — {{ $p->patient_id }} ({{ $p->phone }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">Need a new patient profile? <a href="{{ route('patients.index') }}" class="text-teal-800 font-bold hover:underline">Register Patient</a></p>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Attending Doctor & Specialization *</label>
                        <select name="doctor_id" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                            <option value="">Select Doctor...</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ (old('doctor_id', $selectedDoctorId) == $doc->id) ? 'selected' : '' }}>
                                    {{ $doc->name }} ({{ $doc->qualifications ?? 'MBBS, MD' }}) — Fee: ₹{{ number_format($doc->consultation_fee ?? 500, 0) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Date, Time Slot & Consultation Type -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h2 class="text-base font-heading font-extrabold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 text-xs flex items-center justify-center mr-2 font-black">2</span>
                    Appointment Schedule & Consultation Details
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Appointment Date *</label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Time Slot *</label>
                        <input type="time" name="appointment_time" value="{{ old('appointment_time', '09:30') }}" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Appointment Type *</label>
                        <select name="appointment_type" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                            <option value="General Consultation">General Consultation</option>
                            <option value="Follow-up">Follow-up Visit</option>
                            <option value="Routine Checkup">Routine Checkup</option>
                            <option value="Specialist Care">Specialist Care & Evaluation</option>
                            <option value="Emergency">Emergency Triage</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Initial Triage Status *</label>
                        <select name="status" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                            <option value="Confirmed">Confirmed (Scheduled)</option>
                            <option value="Checked In">Checked In (In Waiting Room)</option>
                            <option value="Pending">Pending Confirmation</option>
                            <option value="In Consultation">In Consultation</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Payment Status *</label>
                        <select name="payment_status" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                            <option value="Unpaid">Unpaid (Pay after visit)</option>
                            <option value="Paid">Paid (Cash / UPI collected)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Clinical Complaints & Notes -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h2 class="text-base font-heading font-extrabold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 text-xs flex items-center justify-center mr-2 font-black">3</span>
                    Chief Complaints & Triage Notes
                </h2>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Reason for Visit / Symptoms</label>
                    <textarea name="reason_for_visit" rows="2" placeholder="e.g. Patient complains of chest tightness and blood pressure evaluation..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900">{{ old('reason_for_visit') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Additional Triage & Front-Desk Notes</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Fasting required for blood test, patient requested morning slot..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Action Submit Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('appointments.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-teal-900/20 transition-all transform hover:-translate-y-0.5">
                    Save Appointment Schedule →
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
