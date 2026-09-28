@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'overview' }">
    
    <!-- Profile Banner Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-card relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start space-x-5">
                <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-3xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white font-heading font-extrabold text-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-teal-600/25">
                    {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 tracking-tight">{{ $patient->full_name }}</h1>
                        <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-teal-50 text-teal-800 border border-teal-200/80">
                            {{ $patient->patient_id }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $patient->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                            ● {{ $patient->status }}
                        </span>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-4 mt-2.5 text-xs font-semibold text-slate-500">
                        <span>👤 {{ $patient->gender }}, {{ $patient->age ?? 'N/A' }} yrs</span>
                        <span>•</span>
                        <span>🩸 Blood: <strong class="text-slate-900 font-extrabold">{{ $patient->blood_group ?? 'N/A' }}</strong></span>
                        <span>•</span>
                        <span>📞 {{ $patient->phone }}</span>
                        <span>•</span>
                        <span>✉️ {{ $patient->email ?? 'No email' }}</span>
                    </div>

                    @if($patient->allergies)
                        <div class="mt-3 inline-flex items-center px-3.5 py-1.5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-extrabold shadow-sm">
                            ⚠️ Allergies: {{ $patient->allergies }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Workflow Actions -->
            <div class="flex flex-wrap items-center gap-2.5 self-start md:self-auto border-t md:border-t-0 pt-4 md:pt-0 border-slate-100">
                <a href="{{ route('consultations.create', ['patient_id' => $patient->id]) }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-teal-600/25 transition-all transform hover:-translate-y-0.5 flex items-center">
                    🩺 Start Consultation
                </a>
                <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-2xl transition-all transform hover:-translate-y-0.5">
                    + Prescription
                </a>
                <a href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-2xl transition-all">
                    + Invoice
                </a>
            </div>
        </div>

        <!-- Section Tabs -->
        <div class="flex items-center space-x-2 mt-8 border-b border-slate-200/80 overflow-x-auto text-xs font-bold">
            <button @click="tab = 'overview'" :class="tab === 'overview' ? 'border-teal-600 text-teal-700 border-b-2 font-extrabold' : 'text-slate-500 hover:text-slate-900'" class="pb-3.5 px-4 transition-all whitespace-nowrap">
                Personal & Medical Overview
            </button>
            <button @click="tab = 'appointments'" :class="tab === 'appointments' ? 'border-teal-600 text-teal-700 border-b-2 font-extrabold' : 'text-slate-500 hover:text-slate-900'" class="pb-3.5 px-4 transition-all whitespace-nowrap">
                Appointments ({{ count($patient->appointments) }})
            </button>
            <button @click="tab = 'consultations'" :class="tab === 'consultations' ? 'border-teal-600 text-teal-700 border-b-2 font-extrabold' : 'text-slate-500 hover:text-slate-900'" class="pb-3.5 px-4 transition-all whitespace-nowrap">
                Consultations ({{ count($patient->consultations) }})
            </button>
            <button @click="tab = 'prescriptions'" :class="tab === 'prescriptions' ? 'border-teal-600 text-teal-700 border-b-2 font-extrabold' : 'text-slate-500 hover:text-slate-900'" class="pb-3.5 px-4 transition-all whitespace-nowrap">
                Prescriptions ({{ count($patient->prescriptions) }})
            </button>
            <button @click="tab = 'invoices'" :class="tab === 'invoices' ? 'border-teal-600 text-teal-700 border-b-2 font-extrabold' : 'text-slate-500 hover:text-slate-900'" class="pb-3.5 px-4 transition-all whitespace-nowrap">
                Invoices & Payments ({{ count($patient->invoices) }})
            </button>
            <button @click="tab = 'documents'" :class="tab === 'documents' ? 'border-teal-600 text-teal-700 border-b-2 font-extrabold' : 'text-slate-500 hover:text-slate-900'" class="pb-3.5 px-4 transition-all whitespace-nowrap">
                Attachments & Documents ({{ count($patient->documents) }})
            </button>
        </div>
    </div>

    <!-- Tab 1: Personal & Medical Overview -->
    <div x-show="tab === 'overview'" class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-cloak>
        <!-- Demographics Card -->
        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-card space-y-4">
            <h3 class="text-xs font-extrabold text-teal-700 uppercase tracking-wider border-b border-slate-100 pb-3">Demographic & Contact Info</h3>
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold block">Full Name</span>
                    <span class="font-extrabold text-slate-900 text-sm">{{ $patient->full_name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Date of Birth</span>
                    <span class="font-bold text-slate-900">{{ $patient->dob ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Gender & Age</span>
                    <span class="font-bold text-slate-900">{{ $patient->gender }}, {{ $patient->age }} years</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Blood Group</span>
                    <span class="font-extrabold text-teal-700">{{ $patient->blood_group ?? 'Unspecified' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Phone</span>
                    <span class="font-bold text-slate-900">{{ $patient->phone }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Email</span>
                    <span class="font-bold text-slate-900">{{ $patient->email ?? 'N/A' }}</span>
                </div>
                <div class="col-span-2 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-semibold block">Full Residential Address</span>
                    <span class="font-bold text-slate-900">{{ $patient->address }}, {{ $patient->city }}, {{ $patient->state }} {{ $patient->pin_code }}</span>
                </div>
            </div>
        </div>

        <!-- Medical History Card -->
        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-card space-y-4">
            <h3 class="text-xs font-extrabold text-teal-700 uppercase tracking-wider border-b border-slate-100 pb-3">Medical Background & Contacts</h3>
            <div class="space-y-3 text-xs">
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-extrabold uppercase block mb-1">Existing Conditions</span>
                    <span class="font-bold text-slate-900">{{ $patient->existing_conditions ?: 'None reported' }}</span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-extrabold uppercase block mb-1">Current Medications</span>
                    <span class="font-bold text-slate-900">{{ $patient->current_medications ?: 'None reported' }}</span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-extrabold uppercase block mb-1">Previous Surgical / Hospitalization History</span>
                    <span class="font-bold text-slate-900">{{ $patient->previous_history ?: 'None reported' }}</span>
                </div>
                <div class="p-3.5 bg-teal-50/50 rounded-2xl border border-teal-100">
                    <span class="text-teal-700 font-extrabold uppercase block mb-1">Emergency Contact</span>
                    <span class="font-bold text-slate-900">{{ $patient->emergency_contact_name }} ({{ $patient->emergency_contact_phone }})</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Appointment History -->
    <div x-show="tab === 'appointments'" class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" x-cloak>
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900">Appointment History</h3>
            <a href="{{ route('appointments.index') }}" class="text-xs font-bold text-teal-600 hover:underline">+ Schedule New</a>
        </div>
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase">
                <tr>
                    <th class="p-4">Appt #</th>
                    <th class="p-4">Date & Time</th>
                    <th class="p-4">Type</th>
                    <th class="p-4">Doctor</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($patient->appointments as $apt)
                    <tr class="hover:bg-slate-50/80">
                        <td class="p-4 font-extrabold text-teal-700">{{ $apt->appointment_number }}</td>
                        <td class="p-4 font-bold text-slate-900">{{ $apt->appointment_date }} • {{ $apt->appointment_time }}</td>
                        <td class="p-4 font-semibold text-slate-700">{{ $apt->appointment_type }}</td>
                        <td class="p-4 font-medium text-slate-700">{{ $apt->doctor->name ?? 'Dr. Robert Carter' }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-teal-50 text-teal-800 border border-teal-200">
                                {{ $apt->status }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('consultations.create', ['appointment_id' => $apt->id]) }}" class="text-teal-600 font-extrabold hover:underline">Consult →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-400 font-semibold">No appointment records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tab 3: Consultation History -->
    <div x-show="tab === 'consultations'" class="space-y-4" x-cloak>
        @forelse($patient->consultations as $c)
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <span class="font-extrabold text-slate-900 text-sm">Consultation on {{ $c->consultation_date }}</span>
                        <span class="text-xs text-slate-400 block font-medium">Attending Physician: {{ $c->doctor->name ?? 'Dr. Robert Carter' }}</span>
                    </div>
                    @if($c->prescription)
                        <a href="{{ route('prescriptions.show', $c->prescription->id) }}" class="px-3.5 py-1.5 bg-teal-50 text-teal-700 font-bold text-xs rounded-xl border border-teal-200 hover:bg-teal-100">
                            View RX: {{ $c->prescription->prescription_number }}
                        </a>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="bg-slate-50 p-3.5 rounded-2xl border">
                        <strong class="text-slate-400 uppercase block mb-1">Symptoms</strong>
                        <p class="text-slate-800 font-semibold">{{ $c->symptoms ?: 'N/A' }}</p>
                    </div>
                    <div class="bg-teal-50/60 p-3.5 rounded-2xl border border-teal-100">
                        <strong class="text-teal-700 uppercase block mb-1">Diagnosis</strong>
                        <p class="text-teal-900 font-extrabold text-sm">{{ $c->diagnosis }}</p>
                    </div>
                    <div class="md:col-span-2 bg-slate-50 p-3.5 rounded-2xl border">
                        <strong class="text-slate-400 uppercase block mb-1">Medical Notes & Treatment Plan</strong>
                        <p class="text-slate-800 font-medium leading-relaxed">{{ $c->medical_notes }} {{ $c->treatment_plan }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-10 text-center text-slate-400 rounded-3xl border shadow-card font-semibold">No consultation notes recorded yet.</div>
        @endforelse
    </div>

    <!-- Tab 4: Prescriptions -->
    <div x-show="tab === 'prescriptions'" class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" x-cloak>
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900">Digital Prescriptions History</h3>
            <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="text-xs font-bold text-teal-600 hover:underline">+ Create New RX</a>
        </div>
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase">
                <tr>
                    <th class="p-4">RX #</th>
                    <th class="p-4">Date</th>
                    <th class="p-4">Diagnosis</th>
                    <th class="p-4">Prescribed Medicines</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($patient->prescriptions as $rx)
                    <tr class="hover:bg-slate-50">
                        <td class="p-4 font-extrabold text-teal-700">{{ $rx->prescription_number }}</td>
                        <td class="p-4 font-bold text-slate-800">{{ $rx->prescription_date }}</td>
                        <td class="p-4 font-extrabold text-slate-900">{{ $rx->diagnosis }}</td>
                        <td class="p-4 text-slate-700 font-medium">
                            {{ implode(', ', $rx->items->pluck('medicine_name')->toArray()) }}
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <a href="{{ route('prescriptions.show', $rx->id) }}" title="View Prescription" class="p-2 bg-slate-100 hover:bg-teal-50 text-slate-700 hover:text-teal-800 font-bold rounded-xl transition-all inline-flex items-center justify-center border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('prescriptions.print', $rx->id) }}" target="_blank" title="Print PDF Document" class="p-2 bg-teal-800 hover:bg-teal-900 text-white font-bold rounded-xl transition-all inline-flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-400 font-semibold">No prescription records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tab 5: Invoices & Payments -->
    <div x-show="tab === 'invoices'" class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden" x-cloak>
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900">Invoices & Billing Statements</h3>
            <a href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}" class="text-xs font-bold text-teal-600 hover:underline">+ Generate Invoice</a>
        </div>
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase">
                <tr>
                    <th class="p-4">Invoice #</th>
                    <th class="p-4">Date</th>
                    <th class="p-4">Total Billed</th>
                    <th class="p-4">Paid</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($patient->invoices as $inv)
                    <tr class="hover:bg-slate-50">
                        <td class="p-4 font-extrabold text-teal-700">{{ $inv->invoice_number }}</td>
                        <td class="p-4 font-semibold text-slate-800">{{ $inv->invoice_date }}</td>
                        <td class="p-4 font-extrabold text-slate-900">${{ number_format($inv->total_amount, 2) }}</td>
                        <td class="p-4 text-emerald-600 font-extrabold">${{ number_format($inv->paid_amount, 2) }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $inv->status === 'Paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700' }}">
                                {{ $inv->status }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <a href="{{ route('invoices.show', $inv->id) }}" class="text-teal-600 font-extrabold hover:underline">View & Record Payment</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-400 font-semibold">No invoice records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tab 6: Documents & Attachments -->
    <div x-show="tab === 'documents'" class="space-y-6" x-cloak>
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Upload Lab Report / Document</h3>
            <form method="POST" action="{{ route('patients.upload-document', $patient->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Document Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Lipid Profile Lab Results" class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl focus:border-teal-500" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Select File (PDF, Image) *</label>
                    <input type="file" name="document" required class="w-full px-3.5 py-2 bg-slate-50 border rounded-xl" />
                </div>
                <div class="sm:col-span-1 flex items-end">
                    <button type="submit" class="w-full py-2.5 bg-teal-600 text-white font-bold rounded-xl shadow-md shadow-teal-600/20">Upload Document</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($patient->documents as $doc)
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-card flex items-center justify-between">
                    <div>
                        <p class="font-extrabold text-slate-900 text-xs">{{ $doc->title }}</p>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ strtoupper($doc->file_type) }} • {{ round($doc->file_size / 1024) }} KB • {{ $doc->created_at->format('M d, Y') }}</p>
                    </div>
                    <a href="{{ $doc->file_path }}" target="_blank" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition-all">View File</a>
                </div>
            @empty
                <div class="sm:col-span-2 bg-white p-8 text-center text-slate-400 text-xs rounded-3xl border shadow-card font-semibold">No documents attached yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
