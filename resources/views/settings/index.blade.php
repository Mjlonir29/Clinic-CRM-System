@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
    tab: 'clinic_profile',
    slotDuration: '{{ $settings['appointment_duration'] ?? '30' }}',
    hasUnsavedChanges: false
}">

    <!-- Header Breadcrumbs & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinic Admin</span>
                <span>/</span>
                <span class="text-teal-800">Clinic Settings & Configuration</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Clinic Settings</h1>
                <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200 rounded-md">Facility ID: #FAC-8092</span>
                <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200/60 rounded-md">● Active System</span>
            </div>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Configure facility identity, doctor credentials, operating schedules, prescription templates, and invoice parameters.</p>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Settings Tab Navigation Bar -->
    <div class="bg-white p-2 rounded-3xl border border-slate-200/90 shadow-card flex items-center space-x-1.5 overflow-x-auto text-xs font-extrabold">
        <button type="button" @click="tab = 'clinic_profile'" :class="tab === 'clinic_profile' ? 'bg-teal-800 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2.5 rounded-2xl transition-all whitespace-nowrap flex items-center space-x-2 cursor-pointer">
            <span>🏥 Clinic Profile & Operating Schedule</span>
        </button>
        <button type="button" @click="tab = 'doctor_credentials'" :class="tab === 'doctor_credentials' ? 'bg-teal-800 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2.5 rounded-2xl transition-all whitespace-nowrap flex items-center space-x-2 cursor-pointer">
            <span>👨‍⚕️ Doctor & Credentials</span>
        </button>
        <button type="button" @click="tab = 'appointment_schedule'" :class="tab === 'appointment_schedule' ? 'bg-teal-800 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2.5 rounded-2xl transition-all whitespace-nowrap flex items-center space-x-2 cursor-pointer">
            <span>📅 Appointment Schedule</span>
        </button>
        <button type="button" @click="tab = 'rx_template'" :class="tab === 'rx_template' ? 'bg-teal-800 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2.5 rounded-2xl transition-all whitespace-nowrap flex items-center space-x-2 cursor-pointer">
            <span>💊 Prescription Template</span>
        </button>
        <button type="button" @click="tab = 'invoice_tax'" :class="tab === 'invoice_tax' ? 'bg-teal-800 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2.5 rounded-2xl transition-all whitespace-nowrap flex items-center space-x-2 cursor-pointer">
            <span>💳 Invoice & Tax Settings</span>
        </button>
    </div>

    <!-- Form Section -->
    <form method="POST" action="{{ route('settings.update') }}" @input="hasUnsavedChanges = true" class="space-y-6">
        @csrf
        <input type="hidden" name="group" :value="tab">

        <!-- ==================== TAB 1: CLINIC PROFILE & OPERATING SCHEDULE ==================== -->
        <div x-show="tab === 'clinic_profile'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Section 01: Facility Identity & Registration -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 01</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Facility Identity & Registration</h3>
                    </div>
                    <span class="text-[10px] font-black text-teal-800 bg-teal-50 px-2 py-0.5 rounded">Clinic Info</span>
                </div>

                <!-- Clinic Logo Uploader Box -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl bg-teal-800 text-white flex items-center justify-center font-heading font-black text-xl shadow-md flex-shrink-0">
                        Ekta
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-heading font-black text-xs text-slate-900">Clinic Emblem & Watermark</h4>
                        <p class="text-[10px] text-slate-500 font-medium">Drop your clinic logo here or browse file (PNG, SVG, max 2MB). Recommended size: 400×400px square.</p>
                        <div class="flex items-center space-x-2 pt-1">
                            <button type="button" class="px-3 py-1 bg-white hover:bg-slate-100 text-slate-800 font-extrabold text-[10px] rounded-lg border border-slate-200">Upload Logo</button>
                        </div>
                    </div>
                </div>

                <!-- Legal Name & Tax Registration -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Official Clinic Legal Name *</label>
                        <input type="text" name="clinic_name" value="{{ $settings['clinic_name'] ?? 'Ekta Heart & Multispeciality Clinic' }}" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-black text-slate-900" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">State Registration Number</label>
                            <input type="text" name="registration_number" value="{{ $settings['registration_number'] ?? 'MCI-REG-2018-9941A' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold" />
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">GSTIN / Tax ID</label>
                            <input type="text" name="tax_id" value="{{ $settings['tax_id'] ?? 'GSTIN-07AAAAA0000A1Z5' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold" />
                        </div>
                    </div>
                </div>

                <!-- Contact & Reception Channels -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider">Contact & Public Reception Channels</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Official Email</label>
                            <input type="email" name="clinic_email" value="{{ $settings['clinic_email'] ?? 'contact@ektaclinic.in' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Emergency Reception</label>
                            <input type="text" name="clinic_phone" value="{{ $settings['clinic_phone'] ?? '+91 98765 43210' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Public Portal URL</label>
                            <input type="text" name="clinic_website" value="{{ $settings['clinic_website'] ?? 'https://ektaclinic.in/book' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium" />
                        </div>
                    </div>
                </div>

                <!-- Physical Address -->
                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider">Physical Clinic Address</h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Street Address</label>
                        <input type="text" name="clinic_address" value="{{ $settings['clinic_address'] ?? '742 Park Street, Connaught Place' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-medium" />
                    </div>
                </div>
            </div>

            <!-- Section 02: Consultation & Operating Hours -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 02</span>
                            <h3 class="font-heading font-black text-base text-slate-900">Consultation & Operating Hours</h3>
                        </div>
                        <span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded">Active Schedule</span>
                    </div>

                    <!-- Working Days Selector -->
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-extrabold text-slate-700">Clinical Working Days</span>
                            <span class="text-[10px] font-bold text-teal-800">Mon - Sat Operating</span>
                        </div>
                        <div class="grid grid-cols-7 gap-1.5 text-center text-xs font-extrabold">
                            <div class="p-2.5 rounded-xl bg-teal-800 text-white shadow-2xs">Mon<br><span class="text-[9px] opacity-80">09-19</span></div>
                            <div class="p-2.5 rounded-xl bg-teal-800 text-white shadow-2xs">Tue<br><span class="text-[9px] opacity-80">09-19</span></div>
                            <div class="p-2.5 rounded-xl bg-teal-800 text-white shadow-2xs">Wed<br><span class="text-[9px] opacity-80">09-19</span></div>
                            <div class="p-2.5 rounded-xl bg-teal-800 text-white shadow-2xs">Thu<br><span class="text-[9px] opacity-80">09-19</span></div>
                            <div class="p-2.5 rounded-xl bg-teal-800 text-white shadow-2xs">Fri<br><span class="text-[9px] opacity-80">09-19</span></div>
                            <div class="p-2.5 rounded-xl bg-teal-800 text-white shadow-2xs">Sat<br><span class="text-[9px] opacity-80">09-17</span></div>
                            <div class="p-2.5 rounded-xl bg-rose-50 text-rose-600 border border-rose-100">Sun<br><span class="text-[9px] opacity-80">Off</span></div>
                        </div>
                    </div>

                    <!-- Daily Timing Window -->
                    <div class="grid grid-cols-2 gap-4 mt-5">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Clinic Opening</span>
                            <input type="text" name="working_hours" value="{{ $settings['working_hours'] ?? 'Mon-Sat: 9:00 AM - 8:00 PM' }}" class="w-full px-2 py-1 text-xs bg-white border rounded-lg font-bold text-slate-900" />
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Break Interval</span>
                            <input type="text" name="break_time" value="{{ $settings['break_time'] ?? '1:00 PM - 2:00 PM' }}" class="w-full px-2 py-1 text-xs bg-white border rounded-lg font-bold text-slate-900" />
                        </div>
                    </div>

                    <!-- Fees -->
                    <div class="grid grid-cols-2 gap-4 mt-5 text-xs">
                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Standard Consultation Fee (₹)</label>
                            <input type="text" name="consultation_fee" value="{{ $settings['consultation_fee'] ?? '500.00' }}" class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold text-slate-900" />
                        </div>
                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Currency Symbol</label>
                            <input type="text" name="currency" value="{{ $settings['currency'] ?? '₹' }}" class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold text-slate-900" />
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ==================== TAB 2: DOCTOR & CREDENTIALS ==================== -->
        <div x-show="tab === 'doctor_credentials'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Section 01: Doctor Profile -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 01</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Doctor Profile & Medical License</h3>
                    </div>
                    <span class="text-[10px] font-black text-teal-800 bg-teal-50 px-2 py-0.5 rounded">Medical Credentials</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Attending Doctor Full Name *</label>
                        <input type="text" name="doctor_name" value="{{ $settings['doctor_name'] ?? 'Dr. Rajesh Sharma, MD' }}" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-black text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Medical Qualifications</label>
                        <input type="text" name="doctor_qualification" value="{{ $settings['doctor_qualification'] ?? 'MBBS, MD (General Medicine), DM (Cardiology)' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Clinical Specialization</label>
                        <input type="text" name="doctor_specialization" value="{{ $settings['doctor_specialization'] ?? 'Cardiology & General Medicine' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">MCI / NMC Registration No.</label>
                            <input type="text" name="doctor_registration_number" value="{{ $settings['doctor_registration_number'] ?? 'MCI-2024-98765' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-mono text-slate-900" />
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Direct Contact Phone</label>
                            <input type="text" name="doctor_phone" value="{{ $settings['doctor_phone'] ?? '+91 98765 43210' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 02: Digital Signature & Verification -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 02</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Digital Signature & RX Authorization</h3>
                    </div>
                    <span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded">Verified Signature</span>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-2xl bg-white border border-slate-300 flex items-center justify-center font-script text-xl text-teal-800 shadow-xs">
                            ✍️ Signature
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-xs text-slate-900">Doctor Digital Stamp & Signature</h4>
                            <p class="text-[10px] text-slate-500">Upload high-resolution transparent PNG signature for automated digital prescription signing.</p>
                            <button type="button" class="mt-2 px-3 py-1 bg-white hover:bg-slate-100 text-slate-800 font-extrabold text-[10px] rounded-lg border border-slate-200">Upload Signature PNG</button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Doctor Official Email</label>
                    <input type="email" name="doctor_email" value="{{ $settings['doctor_email'] ?? 'doctor@cliniccrm.com' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold" />
                </div>
            </div>

        </div>

        <!-- ==================== TAB 3: APPOINTMENT SCHEDULE ==================== -->
        <div x-show="tab === 'appointment_schedule'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 01</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Appointment Slot Duration & Limits</h3>
                    </div>
                    <span class="text-[10px] font-black text-teal-800 bg-teal-50 px-2 py-0.5 rounded">Slot Control</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Default Slot Duration (Minutes)</label>
                        <select name="appointment_duration" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-extrabold text-slate-900">
                            <option value="15" {{ ($settings['appointment_duration'] ?? '') == '15' ? 'selected' : '' }}>15 Minutes (Quick triage & follow-up)</option>
                            <option value="30" {{ ($settings['appointment_duration'] ?? '30') == '30' ? 'selected' : '' }}>30 Minutes (Standard clinical consultation - Recommended)</option>
                            <option value="45" {{ ($settings['appointment_duration'] ?? '') == '45' ? 'selected' : '' }}>45 Minutes (Specialist evaluation & procedure)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Maximum Daily Patient Limit</label>
                        <input type="number" name="max_appointments_per_day" value="{{ $settings['max_appointments_per_day'] ?? '24' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-extrabold text-slate-900" />
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 02</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Cancellation Policy & Rules</h3>
                    </div>
                    <span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded">Policy Rules</span>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Cancellation & Rescheduling Rules</label>
                    <textarea name="cancellation_rules" rows="4" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800">{{ $settings['cancellation_rules'] ?? 'Appointments must be cancelled at least 2 hours prior to scheduled time.' }}</textarea>
                </div>
            </div>

        </div>

        <!-- ==================== TAB 4: PRESCRIPTION TEMPLATE ==================== -->
        <div x-show="tab === 'rx_template'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 01</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Prescription Header & Branding</h3>
                    </div>
                    <span class="text-[10px] font-black text-teal-800 bg-teal-50 px-2 py-0.5 rounded">RX Header</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Prescription Header Title</label>
                        <input type="text" name="prescription_header" value="{{ $settings['prescription_header'] ?? 'Ekta Heart & Multispeciality Clinic - Digital Health Record' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Prescription Footer Note</label>
                        <input type="text" name="prescription_footer" value="{{ $settings['prescription_footer'] ?? 'Wish you a speedy recovery! For emergencies, please call +91 98765 43210.' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" />
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 02</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Default Clinical Advice Notes</h3>
                    </div>
                    <span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded">Advice Notes</span>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Default Advice Instructions</label>
                    <textarea name="default_instructions" rows="4" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800">{{ $settings['default_instructions'] ?? 'Take all medicines after meals with plenty of warm water unless specified otherwise.' }}</textarea>
                </div>
            </div>

        </div>

        <!-- ==================== TAB 5: INVOICE & TAX SETTINGS ==================== -->
        <div x-show="tab === 'invoice_tax'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 01</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Invoice Numbering & GST Tax Rate</h3>
                    </div>
                    <span class="text-[10px] font-black text-teal-800 bg-teal-50 px-2 py-0.5 rounded">Tax Setup</span>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Invoice Prefix</label>
                            <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-mono text-slate-900" />
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">GST Tax Percentage (%)</label>
                            <input type="text" name="tax_percentage" value="{{ $settings['tax_percentage'] ?? '5.0' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Payment Terms</label>
                        <input type="text" name="payment_terms" value="{{ $settings['payment_terms'] ?? 'Payment due upon receipt of invoice.' }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" />
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-card space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">SECTION 02</span>
                        <h3 class="font-heading font-black text-base text-slate-900">Receipt Footer & Notes</h3>
                    </div>
                    <span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded">Receipt Footer</span>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Invoice Footer Note</label>
                    <textarea name="invoice_footer" rows="4" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800">{{ $settings['invoice_footer'] ?? 'Thank you for choosing Ekta Care Clinic. Keep this receipt for your records.' }}</textarea>
                </div>
            </div>

        </div>

        <!-- Floating Unsaved Changes Bar -->
        <div x-show="hasUnsavedChanges" class="p-4 rounded-3xl bg-slate-900 text-white shadow-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-slate-800" x-cloak>
            <div class="flex items-center space-x-3">
                <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                <div>
                    <span class="font-heading font-black text-xs block">You have unsaved changes</span>
                    <span class="text-[10px] text-slate-400 font-medium">Click Save Settings to apply your updates</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" @click="hasUnsavedChanges = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl cursor-pointer">Cancel & Revert</button>
                <button type="submit" class="px-6 py-2 bg-teal-800 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/40 cursor-pointer">Save Settings →</button>
            </div>
        </div>

    </form>

</div>
@endsection
