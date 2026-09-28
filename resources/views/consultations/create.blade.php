@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Patient Consultation Record</h1>
            <p class="text-xs font-semibold text-teal-600 mt-1">
                Consulting Patient: <strong class="text-slate-900">{{ $patient->full_name }}</strong> ({{ $patient->patient_id }} - {{ $patient->gender }}, {{ $patient->age }}y)
            </p>
        </div>
        <a href="{{ route('patients.show', $patient->id) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 px-3.5 py-2 rounded-xl">
            ← Patient Profile
        </a>
    </div>

    <!-- Active Patient Quick Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-4 text-xs">
        <div>
            <span class="text-slate-400 font-bold uppercase block">Phone / Email</span>
            <span class="font-bold text-slate-900">{{ $patient->phone }} • {{ $patient->email ?? 'N/A' }}</span>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase block">Known Allergies</span>
            <span class="font-bold text-rose-600">{{ $patient->allergies ?: 'None reported' }}</span>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase block">Existing Conditions</span>
            <span class="font-bold text-slate-800">{{ $patient->existing_conditions ?: 'None reported' }}</span>
        </div>
        @if($appointment)
            <div class="bg-teal-50 border border-teal-200 px-3 py-1.5 rounded-2xl">
                <span class="text-teal-600 font-bold uppercase block text-[10px]">Tied Appointment</span>
                <span class="font-extrabold text-teal-800">{{ $appointment->appointment_number }} ({{ $appointment->appointment_type }})</span>
            </div>
        @endif
    </div>

    <!-- Consultation Form -->
    <form method="POST" action="{{ route('consultations.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
        @if($appointment)
            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
        @endif

        <!-- Vitals Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-6 h-6 rounded-md bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs mr-2">🩺</span>
                Vital Signs & Metrics
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Blood Pressure</label>
                    <input type="text" name="bp" placeholder="120/80" value="120/80" class="w-full px-3 py-2 bg-slate-50 border rounded-xl focus:border-teal-500" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pulse Rate</label>
                    <input type="text" name="pulse" placeholder="72 bpm" value="75 bpm" class="w-full px-3 py-2 bg-slate-50 border rounded-xl focus:border-teal-500" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Temperature</label>
                    <input type="text" name="temp" placeholder="98.6 °F" value="98.6 °F" class="w-full px-3 py-2 bg-slate-50 border rounded-xl focus:border-teal-500" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Weight</label>
                    <input type="text" name="weight" placeholder="70 kg" value="72 kg" class="w-full px-3 py-2 bg-slate-50 border rounded-xl focus:border-teal-500" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Height</label>
                    <input type="text" name="height" placeholder="175 cm" value="172 cm" class="w-full px-3 py-2 bg-slate-50 border rounded-xl focus:border-teal-500" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">SPO2 %</label>
                    <input type="text" name="spo2" placeholder="99%" value="99%" class="w-full px-3 py-2 bg-slate-50 border rounded-xl focus:border-teal-500" />
                </div>
            </div>
        </div>

        <!-- Clinical Clinical Findings -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-6 h-6 rounded-md bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs mr-2">📋</span>
                Clinical Findings & Medical Evaluation
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Presenting Symptoms *</label>
                    <textarea name="symptoms" rows="3" required placeholder="Describe chief complaints and symptoms..." class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-500">Throbbing headache, fatigue, mild dizziness</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Diagnosis *</label>
                    <textarea name="diagnosis" rows="3" required placeholder="Primary diagnosis and ICD observations..." class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-500">Acute Stress Migraine & Mild Tension Headache</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Clinical Notes & Examination Details</label>
                <textarea name="medical_notes" rows="3" placeholder="Physical examination observations, lab findings, general notes..." class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-500">Pupils equal and reactive to light. No focal neurological deficits. Neck supple without lymphadenopathy.</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Treatment Plan & Non-Pharmacological Advice</label>
                <textarea name="treatment_plan" rows="2" placeholder="Dietary instructions, rest, lifestyle advice..." class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-500">Adequate fluid intake (min 3L/day), stress management, avoid bright screens, 8 hours sleep.</textarea>
            </div>
        </div>

        <!-- Workflow Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
            <button type="submit" name="action" value="save_only" class="w-full sm:w-auto px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition-all">
                Save Consultation Record
            </button>
            <button type="submit" name="create_prescription" value="1" class="w-full sm:w-auto px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-500/20 transition-all flex items-center justify-center">
                Save & Write Prescription →
            </button>
        </div>
    </form>
</div>
@endsection
