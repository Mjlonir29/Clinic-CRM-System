<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Prescription - {{ $prescription->prescription_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @media print {
            body { background: white; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-slate-100 font-['Plus_Jakarta_Sans'] p-6 min-h-screen">
    
    <div class="no-print max-w-3xl mx-auto mb-4 flex justify-between items-center">
        <a href="{{ route('prescriptions.show', $prescription->id) }}" class="text-xs font-bold text-slate-600 hover:underline">← Back to Dashboard</a>
        <button onclick="window.print()" class="px-5 py-2 bg-teal-600 text-white font-bold text-xs rounded-xl shadow">
            🖨️ Print Prescription Now
        </button>
    </div>

    <div class="max-w-3xl mx-auto bg-white p-10 rounded-2xl shadow-lg border space-y-6">
        <!-- Clinic Branding Header -->
        <div class="flex justify-between border-b-2 border-teal-600 pb-6">
            <div>
                <h1 class="text-2xl font-black text-teal-700">{{ $settings['clinic_name'] ?? 'Metro Care Family Clinic' }}</h1>
                <p class="text-xs text-slate-500 font-medium">{{ $settings['clinic_address'] ?? '742 Evergreen Terrace, Springfield, IL' }}</p>
                <p class="text-xs text-slate-500 font-medium">Ph: {{ $settings['clinic_phone'] ?? '+1 (555) 100-2000' }} • Email: {{ $settings['clinic_email'] ?? 'contact@metrocare.com' }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-base font-extrabold text-slate-900">{{ $settings['doctor_name'] ?? 'Dr. Robert Carter, MD' }}</h2>
                <p class="text-xs text-teal-600 font-bold">{{ $settings['doctor_specialization'] ?? 'Cardiology & General Medicine' }}</p>
                <p class="text-[11px] text-slate-400 font-medium">Reg #: {{ $settings['doctor_registration_number'] ?? 'MED-2024-98765' }}</p>
            </div>
        </div>

        <!-- Demographics -->
        <div class="grid grid-cols-4 gap-4 text-xs bg-slate-50 p-4 rounded-xl border">
            <div>
                <span class="text-slate-400 font-bold uppercase block">Patient Name</span>
                <span class="font-extrabold text-slate-900">{{ $prescription->patient->full_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block">Patient ID / Age</span>
                <span class="font-bold text-slate-800">{{ $prescription->patient->patient_id }} • {{ $prescription->patient->gender }}, {{ $prescription->patient->age }}y</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block">Date</span>
                <span class="font-bold text-slate-800">{{ $prescription->prescription_date }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block">Follow-up</span>
                <span class="font-bold text-teal-700">{{ $prescription->follow_up_date ?: 'As needed' }}</span>
            </div>
        </div>

        @if($prescription->diagnosis)
            <div class="text-xs">
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Diagnosis</span>
                <p class="text-sm font-extrabold text-teal-900">{{ $prescription->diagnosis }}</p>
            </div>
        @endif

        <!-- Rx Symbol & Table -->
        <div class="space-y-3">
            <span class="text-3xl font-black text-teal-600 font-serif">Rx</span>
            <table class="w-full text-left text-xs border border-slate-200 rounded-lg overflow-hidden">
                <thead class="bg-slate-100 text-slate-500 font-bold uppercase">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">Medicine</th>
                        <th class="p-3">Dose & Route</th>
                        <th class="p-3">Frequency (Duration)</th>
                        <th class="p-3">Instructions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($prescription->items as $idx => $item)
                        <tr>
                            <td class="p-3 font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3 font-extrabold text-slate-900 text-sm">{{ $item->medicine_name }}</td>
                            <td class="p-3 font-semibold text-slate-700">{{ $item->dosage }} • {{ $item->route }}</td>
                            <td class="p-3 font-bold text-teal-800">{{ $item->frequency }} ({{ $item->duration }})</td>
                            <td class="p-3 text-slate-600 font-medium">{{ $item->timing }} - {{ $item->instructions }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($prescription->advice || $prescription->tests_recommended)
            <div class="grid grid-cols-2 gap-4 text-xs pt-4 border-t">
                @if($prescription->advice)
                    <div>
                        <strong class="text-slate-400 uppercase block mb-1">Doctor's Advice</strong>
                        <p class="p-3 bg-slate-50 rounded-lg text-slate-800">{{ $prescription->advice }}</p>
                    </div>
                @endif
                @if($prescription->tests_recommended)
                    <div>
                        <strong class="text-slate-400 uppercase block mb-1">Investigations / Tests</strong>
                        <p class="p-3 bg-teal-50 text-teal-900 font-semibold rounded-lg">{{ $prescription->tests_recommended }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Footer -->
        <div class="pt-12 flex justify-between items-end text-xs">
            <div class="text-[11px] text-slate-400">
                <p>{{ $settings['prescription_footer'] ?? 'Wish you a speedy recovery!' }}</p>
            </div>
            <div class="text-center">
                <div class="w-36 border-b border-slate-400 mb-1"></div>
                <p class="font-bold text-slate-800">{{ $settings['doctor_name'] ?? 'Dr. Robert Carter' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
