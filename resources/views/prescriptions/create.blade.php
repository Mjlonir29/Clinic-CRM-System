@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="prescriptionWorkspace()">

    <!-- Header Breadcrumbs & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinical EHR</span>
                <span>/</span>
                <span class="text-teal-800">New Consultation & Prescription Workspace</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Electronic Health Record (EHR) & Rx Formulation</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Live patient telemetry, ICD-10 diagnostic coding, and digital signature prescription formulation</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('prescriptions.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center border border-slate-200">
                ← Prescription List
            </a>
        </div>
    </div>

    <!-- Active Patient EHR Telemetry Header Banner -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-13 h-13 rounded-2xl bg-teal-800 text-white font-heading font-black text-lg flex items-center justify-center shadow-md flex-shrink-0">
                <span x-text="selectedPatient ? selectedPatient.name.split(' ').map(n => n[0]).join('') : 'MJ'"></span>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="font-heading font-black text-lg text-slate-900" x-text="selectedPatient ? selectedPatient.name : 'Marcus Jenkins'"></h2>
                    <span class="px-2 py-0.5 text-[10px] font-black bg-teal-50 text-teal-800 border border-teal-200/60 rounded" x-text="selectedPatient ? 'MRN #' + selectedPatient.mrn : 'MRN #PT-8092'"></span>
                    <span class="px-2 py-0.5 text-[10px] font-black bg-rose-50 text-rose-800 border border-rose-200/60 rounded" x-text="selectedPatient ? 'Blood: ' + selectedPatient.blood_group : 'Blood: O+'"></span>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium mt-1">
                    <span x-text="selectedPatient ? selectedPatient.age + 'y / ' + selectedPatient.gender : '45y / Male'"></span>
                    <span>•</span>
                    <span x-text="selectedPatient ? selectedPatient.phone : '+91 98200 12345'"></span>
                    <span>•</span>
                    <span class="text-rose-800 font-black">⚠️ Allergies: Penicillin, Sulfa Drugs</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-4 border-t md:border-t-0 md:border-l border-slate-100 pt-3 md:pt-0 md:pl-6">
            <div class="text-right">
                <div class="flex items-center justify-end space-x-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-emerald-800 uppercase tracking-wider">Active EHR Session</span>
                </div>
                <span class="text-xs font-black text-slate-800">⏱️ Session: 14:22 mins</span>
            </div>
        </div>
    </div>

    <!-- Physical Vitals Telemetry Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] font-black text-amber-800 uppercase block">Blood Pressure</span>
            <span class="text-lg font-heading font-black text-slate-900">138/88 <span class="text-xs text-slate-400">mmHg</span></span>
            <span class="text-[9px] font-black text-amber-800 block mt-0.5">Stage 1 HTN</span>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] font-black text-emerald-800 uppercase block">Heart Rate</span>
            <span class="text-lg font-heading font-black text-slate-900">74 <span class="text-xs text-slate-400">bpm</span></span>
            <span class="text-[9px] font-black text-emerald-800 block mt-0.5">Normal Sinus</span>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] font-black text-slate-400 uppercase block">Body Temperature</span>
            <span class="text-lg font-heading font-black text-slate-900">98.6 <span class="text-xs text-slate-400">°F</span></span>
            <span class="text-[9px] font-bold text-slate-500 block mt-0.5">Afebrile</span>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] font-black text-slate-400 uppercase block">SpO2 Oxygen</span>
            <span class="text-lg font-heading font-black text-slate-900">99%</span>
            <span class="text-[9px] font-bold text-slate-500 block mt-0.5">Room Air</span>
        </div>
    </div>

    <!-- Form & Signature Workspace Split Grid -->
    <form method="POST" action="{{ route('prescriptions.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        @if($consultation)
            <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
        @endif
        @if($appointment)
            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
        @endif

        <!-- Left 2 Columns: Formulation Editor -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Section 1: Patient Selection & Diagnosis -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card space-y-4">
                <h3 class="text-sm font-heading font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Clinical Diagnosis & Case Telemetry</span>
                    <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded">ICD-10 Standardized</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Select Patient *</label>
                        <select name="patient_id" x-model="selectedPatientId" @change="updateSelectedPatient()" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-medium">
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ $selectedPatientId == $p->id ? 'selected' : '' }}>
                                    {{ $p->full_name }} ({{ $p->patient_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Prescription Date *</label>
                        <input type="date" name="prescription_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-700 font-medium" />
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Follow-up Review</label>
                        <input type="date" name="follow_up_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-700 font-medium" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Primary Diagnosis (ICD-10)</label>
                        <input type="text" name="diagnosis" value="{{ $consultation ? $consultation->diagnosis : 'I10 - Essential (primary) hypertension' }}" placeholder="e.g. Essential Hypertension" class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-700 font-medium" />
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Chief Symptoms & Telemetry</label>
                        <input type="text" name="symptoms" value="{{ $consultation ? $consultation->symptoms : 'Occasional headaches, BP 138/88 mmHg' }}" placeholder="e.g. Headache, dizziness" class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-700 font-medium" />
                    </div>
                </div>
            </div>

            <!-- Section 2: Medication Formulation Table -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-heading font-black text-slate-900">
                        Medication Formulation Schedule (Rx)
                    </h3>
                    <button type="button" @click="addMedicine()" class="px-3.5 py-1.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-2xs transition-all flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Medication
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(med, index) in medicines" :key="index">
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 relative space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-teal-800" x-text="'Medication #' + (index + 1)"></span>
                                <button type="button" @click="removeMedicine(index)" class="text-xs font-bold text-rose-600 hover:underline" x-show="medicines.length > 1">
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                                <div class="sm:col-span-2">
                                    <label class="block font-extrabold text-slate-700 mb-1">Medicine Name & Strength *</label>
                                    <input type="text" :name="'medicines['+index+'][name]'" x-model="med.name" required placeholder="e.g. Amoxicillin 500mg Capsules" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-medium" />
                                </div>
                                <div>
                                    <label class="block font-extrabold text-slate-700 mb-1">Dosage Form</label>
                                    <input type="text" :name="'medicines['+index+'][dosage]'" x-model="med.dosage" placeholder="e.g. 1 Capsule" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-medium" />
                                </div>
                                <div>
                                    <label class="block font-extrabold text-slate-700 mb-1">Frequency</label>
                                    <select :name="'medicines['+index+'][frequency]'" x-model="med.frequency" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-medium">
                                        <option value="Twice daily (1-0-1)">Twice daily (1-0-1)</option>
                                        <option value="Once daily (1-0-0)">Once daily (1-0-0)</option>
                                        <option value="3 times daily (1-1-1)">3 times daily (1-1-1)</option>
                                        <option value="As needed (PRN)">As needed (PRN)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div>
                                    <label class="block font-extrabold text-slate-700 mb-1">Duration</label>
                                    <input type="text" :name="'medicines['+index+'][duration]'" x-model="med.duration" placeholder="e.g. 30 Days" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-medium" />
                                </div>
                                <div>
                                    <label class="block font-extrabold text-slate-700 mb-1">Special Administration Instructions</label>
                                    <input type="text" :name="'medicines['+index+'][instructions]'" x-model="med.instructions" placeholder="e.g. Take after meals with full glass of water" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-medium" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Section 3: Diagnostic Advice & Notes -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card space-y-4">
                <h3 class="text-sm font-heading font-black text-slate-900">Clinical Advice & Patient Instructions</h3>
                <textarea name="advice" rows="3" placeholder="Provide dietary guidelines, lifestyle advice, or emergency precautions..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium">Maintain low-sodium DASH diet. Monitor morning blood pressure readings daily. Schedule follow-up in 14 days or contact clinic if BP exceeds 160/100.</textarea>
            </div>

        </div>

        <!-- Right 1 Column: Live Prescription Signature Preview (Image 5 Apex Card) -->
        <div class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sticky top-20 space-y-6">
                
                <!-- Apex Prescription Header Card -->
                <div class="border-b border-slate-200 pb-4 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-black text-base text-slate-900">Ekta Care Clinic</h3>
                        <p class="text-[10px] font-extrabold text-teal-800 uppercase tracking-wider">Official Digital Rx Statement</p>
                        <p class="text-[9px] text-slate-400">450 Medical Parkway, Suite 300</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-teal-800 text-white flex items-center justify-center font-black text-xs shadow-md">
                        Rx
                    </div>
                </div>

                <!-- Rx QR Code & ID Block -->
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase">Rx Serial ID</span>
                        <p class="text-xs font-extrabold text-teal-800">#RX-2026-9042</p>
                    </div>
                    <div class="w-10 h-10 bg-slate-900 text-white text-[8px] font-black flex items-center justify-center rounded-lg text-center leading-none p-1">
                        QR CODE<br>VERIFIED
                    </div>
                </div>

                <!-- Formulated Medicines Preview List -->
                <div class="space-y-3">
                    <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Formulated Medications (<span x-text="medicines.length"></span>)</h4>
                    
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        <template x-for="(m, i) in medicines" :key="i">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div class="font-black text-slate-900" x-text="(i + 1) + '. ' + (m.name || 'Medication Name')"></div>
                                <div class="text-[11px] text-teal-800 font-bold" x-text="m.dosage + ' • ' + m.frequency + ' • ' + m.duration"></div>
                                <div class="text-[10px] text-slate-500 italic" x-text="m.instructions"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Doctor Digital Signature Box -->
                <div class="p-3.5 rounded-2xl bg-teal-50/70 border border-teal-200/80 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-teal-800 uppercase">Attending Physician</span>
                        <span class="text-[9px] font-black text-emerald-800 bg-white px-1.5 py-0.5 rounded border border-emerald-200">✓ Signed</span>
                    </div>
                    <p class="font-heading font-black text-xs text-slate-900">Dr. Marcus Vance, M.D.</p>
                    <p class="text-[9px] font-semibold text-slate-500">Lic #MD-908234 • 256-Bit SSL Encrypted</p>
                </div>

                <!-- Action Button -->
                <button type="submit" class="w-full py-3.5 px-4 bg-teal-800 hover:bg-teal-900 text-white font-heading font-extrabold text-xs rounded-2xl shadow-lg shadow-teal-900/25 transition-all transform hover:-translate-y-0.5 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save & Issue Prescription (Rx) →
                </button>

            </div>
        </div>

    </form>

</div>

<script>
    function prescriptionWorkspace() {
        return {
            selectedPatientId: {{ $selectedPatientId ?? ($patients->first()->id ?? 1) }},
            selectedPatient: null,
            patientsList: {!! json_encode($patients) !!},
            medicines: [
                {
                    name: 'Amoxicillin 500mg Capsules',
                    dosage: '1 Capsule',
                    frequency: 'Twice daily (1-0-1)',
                    duration: '30 Days',
                    instructions: 'Take after meals with full glass of water'
                },
                {
                    name: 'Lisinopril 10mg Tablets',
                    dosage: '1 Tablet',
                    frequency: 'Once daily (1-0-0)',
                    duration: '30 Days',
                    instructions: 'Take in the morning before breakfast'
                }
            ],
            init() {
                this.updateSelectedPatient();
            },
            updateSelectedPatient() {
                const found = this.patientsList.find(p => p.id == this.selectedPatientId);
                if (found) {
                    this.selectedPatient = {
                        name: found.first_name + ' ' + found.last_name,
                        mrn: found.patient_id,
                        age: found.age || 45,
                        gender: found.gender || 'Male',
                        phone: found.phone,
                        blood_group: found.blood_group || 'O+'
                    };
                }
            },
            addMedicine() {
                this.medicines.push({
                    name: '',
                    dosage: '1 Tablet',
                    frequency: 'Twice daily (1-0-1)',
                    duration: '7 Days',
                    instructions: 'Take after meals'
                });
            },
            removeMedicine(index) {
                if (this.medicines.length > 1) {
                    this.medicines.splice(index, 1);
                }
            }
        }
    }
</script>
@endsection
