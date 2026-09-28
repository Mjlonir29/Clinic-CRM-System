@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Page Header Breadcrumbs & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('patients.index') }}" class="hover:text-teal-800 transition-colors">Patients Directory</a>
                <span>/</span>
                <span class="text-teal-800">Register New Patient</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">Register New Master Patient (MPI)</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Create a complete Electronic Health Record (EHR) profile with demographics, medical history & emergency contact.</p>
        </div>

        <a href="{{ route('patients.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all border border-slate-200 flex items-center self-start sm:self-auto">
            ← Back to Patients Directory
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
            @foreach ($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('patients.store') }}" class="space-y-6">
        @csrf
        
        <!-- Section 1: Demographics & Personal Info -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-card space-y-5">
            <h3 class="text-base font-heading font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xs mr-2.5">1</span>
                Patient Identity & Basic Demographics
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">First Name *</label>
                    <input type="text" name="first_name" required value="{{ old('first_name') }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" placeholder="e.g. Marcus" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Last Name *</label>
                    <input type="text" name="last_name" required value="{{ old('last_name') }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" placeholder="e.g. Vance" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob') }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Age (Years)</label>
                    <input type="number" name="age" value="{{ old('age') }}" placeholder="e.g. 35" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Gender *</label>
                    <select name="gender" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Blood Group *</label>
                    <select name="blood_group" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                        <option value="O+" {{ old('blood_group', 'O+') === 'O+' ? 'selected' : '' }}>O Rh Positive (O+)</option>
                        <option value="A+" {{ old('blood_group') === 'A+' ? 'selected' : '' }}>A Rh Positive (A+)</option>
                        <option value="B+" {{ old('blood_group') === 'B+' ? 'selected' : '' }}>B Rh Positive (B+)</option>
                        <option value="AB+" {{ old('blood_group') === 'AB+' ? 'selected' : '' }}>AB Rh Positive (AB+)</option>
                        <option value="O-" {{ old('blood_group') === 'O-' ? 'selected' : '' }}>O Rh Negative (O-)</option>
                        <option value="A-" {{ old('blood_group') === 'A-' ? 'selected' : '' }}>A Rh Negative (A-)</option>
                        <option value="B-" {{ old('blood_group') === 'B-' ? 'selected' : '' }}>B Rh Negative (B-)</option>
                        <option value="AB-" {{ old('blood_group') === 'AB-' ? 'selected' : '' }}>AB Rh Negative (AB-)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Phone Number *</label>
                    <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="patient@example.com" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Street / Residential Address</label>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="House / Flat No., Street Name, Landmark" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Mumbai" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">State / Province</label>
                    <input type="text" name="state" value="{{ old('state') }}" placeholder="e.g. Maharashtra" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">PIN / Postal Code</label>
                    <input type="text" name="pin_code" value="{{ old('pin_code') }}" placeholder="e.g. 400001" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
            </div>
        </div>

        <!-- Section 2: Medical History -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-card space-y-5">
            <h3 class="text-base font-heading font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xs mr-2.5">2</span>
                Medical History & Allergies
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Known Drug Allergies</label>
                    <textarea name="allergies" rows="2" placeholder="e.g. Penicillin, Sulfa, Aspirin" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900">{{ old('allergies') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Existing Conditions</label>
                    <textarea name="existing_conditions" rows="2" placeholder="e.g. Hypertension, Type 2 Diabetes" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900">{{ old('existing_conditions') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Current Ongoing Medications</label>
                    <textarea name="current_medications" rows="2" placeholder="e.g. Metformin 500mg, Amlodipine 5mg" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900">{{ old('current_medications') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Previous Surgical / Illness History</label>
                    <textarea name="previous_history" rows="2" placeholder="Past surgeries, hospitalizations, trauma..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900">{{ old('previous_history') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Family Medical History</label>
                <input type="text" name="family_history" value="{{ old('family_history') }}" placeholder="Heritable conditions (Cardiac, Diabetes, Cancer...)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
            </div>
        </div>

        <!-- Section 3: Emergency Contact -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-card space-y-5">
            <h3 class="text-base font-heading font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xs mr-2.5">3</span>
                Emergency Contact Details
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Emergency Contact Person & Relation</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="e.g. Sunita Vance (Spouse)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="+91 98765 00000" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
            </div>
        </div>

        <!-- Action Submit Buttons -->
        <div class="pt-2 flex items-center justify-end space-x-4">
            <a href="{{ route('patients.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">Cancel</a>
            <button type="submit" class="px-8 py-3 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-teal-900/20 transition-all transform hover:-translate-y-0.5">
                Save Patient Profile →
            </button>
        </div>
    </form>
</div>
@endsection
