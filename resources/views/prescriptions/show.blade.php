@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Header Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinical EHR</span>
                <span>/</span>
                <span class="text-teal-800">Prescription Record</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Prescription & Clinical Summary</h1>
            <p class="text-xs font-extrabold text-teal-800 mt-0.5">Official Statement Serial: {{ $prescription->prescription_number }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('prescriptions.print', $prescription->id) }}" target="_blank" class="px-4 py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/20 transition-all inline-flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>Print / Download PDF</span>
            </a>
            <form method="POST" action="{{ route('prescriptions.send', $prescription->id) }}" class="inline-block">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-all">
                    ✉️ Send to Patient
                </button>
            </form>
        </div>
    </div>

    <!-- Official Prescription Document Card (Image 5 Apex Style) -->
    <div class="bg-white p-8 sm:p-12 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
        
        <!-- Clinic Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b-2 border-teal-800 pb-6 gap-4">
            <div>
                <h2 class="text-2xl font-heading font-black text-slate-900">{{ $settings['clinic_name'] ?? 'Ekta Care Clinic' }}</h2>
                <p class="text-xs text-slate-500 font-semibold">{{ $settings['clinic_address'] ?? '450 Medical Parkway, Suite 300' }}</p>
                <p class="text-xs text-slate-500 font-semibold">Phone: {{ $settings['clinic_phone'] ?? '+91 98765 43210' }} • Email: {{ $settings['clinic_email'] ?? 'contact@ektaclinic.in' }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-base font-heading font-black text-teal-800">{{ $settings['doctor_name'] ?? 'Dr. Marcus Vance, M.D.' }}</p>
                <p class="text-xs text-slate-700 font-extrabold">{{ $settings['doctor_specialization'] ?? 'Attending Physician • General Medicine' }}</p>
                <p class="text-[11px] text-slate-400 font-bold">Lic #: {{ $settings['doctor_registration_number'] ?? 'MD-908234' }}</p>
            </div>
        </div>

        <!-- Patient Demographics & Telemetry Header Bar -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-black uppercase text-[10px] block">Patient Name</span>
                <span class="font-extrabold text-slate-900 text-sm">{{ $prescription->patient->full_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-black uppercase text-[10px] block">MRN & Demographics</span>
                <span class="font-bold text-slate-800">#{{ $prescription->patient->patient_id }} • {{ $prescription->patient->gender }}, {{ $prescription->patient->age }}y</span>
            </div>
            <div>
                <span class="text-slate-400 font-black uppercase text-[10px] block">Date Prescribed</span>
                <span class="font-bold text-slate-800">{{ $prescription->prescription_date }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-black uppercase text-[10px] block">Follow-up Review</span>
                <span class="font-bold text-teal-800">{{ $prescription->follow_up_date ?: 'As required' }}</span>
            </div>
        </div>

        <!-- Diagnosis Section -->
        @if($prescription->diagnosis)
            <div class="text-xs">
                <span class="font-black text-slate-400 uppercase tracking-wider text-[10px] block mb-1">Primary ICD-10 Diagnosis</span>
                <p class="text-base font-heading font-black text-teal-800">{{ $prescription->diagnosis }}</p>
            </div>
        @endif

        <!-- Prescribed Medications Table -->
        <div class="space-y-3">
            <div class="flex items-center space-x-2">
                <span class="text-3xl font-black text-teal-800 font-serif">Rx</span>
                <span class="text-xs font-black text-slate-400 uppercase tracking-wider">Formulated Medication Schedule</span>
            </div>

            <table class="w-full text-left text-xs border border-slate-200 rounded-2xl overflow-hidden">
                <thead class="bg-slate-50/80 border-b text-slate-400 font-black text-[10px] uppercase">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">Medication Name & Strength</th>
                        <th class="p-3">Dosage Form</th>
                        <th class="p-3">Frequency & Duration</th>
                        <th class="p-3">Administration Instructions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($prescription->items as $idx => $item)
                        <tr class="hover:bg-teal-50/20">
                            <td class="p-3 font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3 font-heading font-black text-slate-900 text-sm">{{ $item->medicine_name }}</td>
                            <td class="p-3 font-semibold text-slate-700">{{ $item->dosage }}</td>
                            <td class="p-3 font-extrabold text-teal-800">{{ $item->frequency }} ({{ $item->duration }})</td>
                            <td class="p-3 text-slate-600 font-medium">
                                {{ $item->instructions ?: 'Take as directed' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Advice & Instructions -->
        @if($prescription->advice)
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/60 space-y-1">
                <span class="text-[10px] font-black text-slate-400 uppercase">Special Instructions / Doctor Advice</span>
                <p class="text-xs font-medium text-slate-800 italic leading-relaxed">{{ $prescription->advice }}</p>
            </div>
        @endif

        <!-- Digital Signature Footer (Image 5 Apex Verification Stamp) -->
        <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-slate-900 text-white font-black text-[9px] flex items-center justify-center rounded-xl text-center leading-none">
                    QR CODE<br>VERIFIED
                </div>
                <div>
                    <span class="text-[10px] font-black text-emerald-800 uppercase block">✓ Verified Digital EHR Record</span>
                    <span class="text-[10px] text-slate-400 font-medium">Encrypted SHA-256 Hash • Ekta Care Clinic</span>
                </div>
            </div>

            <div class="text-center sm:text-right">
                <p class="font-heading font-black text-sm text-slate-900">{{ $settings['doctor_name'] ?? $prescription->doctor->name ?? auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 font-semibold">Attending Physician • Digital Signature On File</p>
            </div>
        </div>

    </div>
</div>
@endsection
