@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header Breadcrumbs & Actions -->
    <div class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('doctors.index') }}" class="hover:text-teal-800">Doctors Directory</a>
                <span>/</span>
                <span class="text-teal-800">Add New Doctor</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Register New Medical Specialist</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Add a new doctor to the clinic roster with full degrees, medical council registration, specialization & consultation rates.</p>
        </div>

        <a href="{{ route('doctors.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all border border-slate-200 flex items-center">
            ← Back to Directory
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
            @foreach ($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Main Registration Form -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sm:p-8">
        <form action="{{ route('doctors.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Basic Identity -->
            <div class="space-y-4">
                <h2 class="text-base font-heading font-extrabold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 text-xs flex items-center justify-center mr-2 font-black">1</span>
                    Doctor Identity & Account Credentials
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Doctor Full Name (with Title) *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Dr. Rajesh Sharma" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">System Username *</label>
                        <input type="text" name="username" value="{{ old('username') }}" required placeholder="e.g. dr.rajesh" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="doctor@cliniccrm.com" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Phone Number *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Account Password *</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                    </div>
                </div>
            </div>

            <!-- Section 2: Medical Degrees & Qualifications -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h2 class="text-base font-heading font-extrabold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 text-xs flex items-center justify-center mr-2 font-black">2</span>
                    Degrees, Qualifications & Specialization
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Medical Degrees & Qualifications *</label>
                        <input type="text" name="qualifications" value="{{ old('qualifications') }}" required placeholder="e.g. MBBS, MD (General Medicine), DM (Cardiology), FCPS" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900" />
                        <span class="text-[10px] text-slate-400 font-medium">List all academic degrees separated by commas</span>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Primary Specialization / Department *</label>
                        <input type="text" name="specialization" value="{{ old('specialization') }}" required placeholder="e.g. Cardiology & General Medicine" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold text-slate-900" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Medical Registration Number (MCI/NMC ID) *</label>
                        <input type="text" name="registration_number" value="{{ old('registration_number') }}" required placeholder="e.g. MCI-2024-98765" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-mono text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Years of Experience</label>
                        <input type="text" name="experience_years" value="{{ old('experience_years') }}" placeholder="e.g. 15 Years" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Consultation Fee (₹) *</label>
                        <input type="number" step="0.01" name="consultation_fee" value="{{ old('consultation_fee', '500.00') }}" required placeholder="500.00" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-extrabold text-slate-900" />
                    </div>
                </div>
            </div>

            <!-- Section 3: Availability & OPD Details -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h2 class="text-base font-heading font-extrabold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 text-xs flex items-center justify-center mr-2 font-black">3</span>
                    Clinic Cabin & OPD Shift Hours
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Cabin / OPD Room Number</label>
                        <input type="text" name="cabin_number" value="{{ old('cabin_number') }}" placeholder="e.g. OPD Cabin 3B - Cardiology Wing" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Working Days & Available Shift Hours</label>
                        <input type="text" name="working_hours" value="{{ old('working_hours') }}" placeholder="e.g. Mon-Sat: 9:00 AM - 5:00 PM" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                    </div>
                </div>
            </div>

            <!-- Action Submit Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('doctors.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-teal-900/20 transition-all transform hover:-translate-y-0.5">
                    Save Doctor Profile →
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
