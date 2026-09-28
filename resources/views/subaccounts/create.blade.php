@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Breadcrumbs & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('subaccounts.index') }}" class="hover:text-teal-800 transition-colors">Staff Directory</a>
                <span>/</span>
                <span class="text-teal-800">Add Staff Account</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">Create New Staff Account</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Admin-configured staff credentials, password, clinic role, professional degrees & access permissions</p>
        </div>

        <a href="{{ route('subaccounts.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all border border-slate-200 flex items-center self-start sm:self-auto">
            ← Back to Staff Directory
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
            @foreach ($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('subaccounts.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{
        selectedRole: 1,
        permissions: ['appointments.view', 'appointments.create', 'appointments.cancel', 'patients.view', 'patients.edit']
    }">
        @csrf

        <!-- Section 1: Staff Identity & Account Credentials -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-card space-y-5">
            <h3 class="text-base font-heading font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xs mr-2.5">1</span>
                Staff Login Credentials & Account Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Staff Full Name (with Title) *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Dr. Anita Roy" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">System Login Username *</label>
                    <input type="text" name="username" required value="{{ old('username') }}" placeholder="e.g. dr.anita" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Email Address *</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="anita@cliniccrm.com" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Clinic Role *</label>
                    <select name="role_id" x-model="selectedRole" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Initial Account Password *</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Account Status *</label>
                    <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-bold text-slate-900">
                        <option value="active">Active Practitioner</option>
                        <option value="inactive">Inactive / On Leave</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Medical Qualifications & OPD Shift (Optional) -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-card space-y-5">
            <h3 class="text-base font-heading font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xs mr-2.5">2</span>
                Medical Qualifications & Clinical Details
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Degrees & Qualifications</label>
                    <input type="text" name="qualifications" value="{{ old('qualifications') }}" placeholder="e.g. MBBS, MD (General Medicine)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Specialization / Department</label>
                    <input type="text" name="specialization" value="{{ old('specialization') }}" placeholder="e.g. General Medicine & Pediatrics" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">MCI / NMC Registration ID</label>
                    <input type="text" name="registration_number" value="{{ old('registration_number') }}" placeholder="e.g. MCI-2024-98765" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-mono text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Cabin / OPD Room</label>
                    <input type="text" name="cabin_number" value="{{ old('cabin_number') }}" placeholder="e.g. OPD Cabin 2A" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Shift Hours / Working Days</label>
                    <input type="text" name="working_hours" value="{{ old('working_hours') }}" placeholder="e.g. Mon-Sat: 9 AM - 5 PM" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
            </div>
        </div>

        <!-- Section 3: Staff Document Storage / Certificate Attachment -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-card space-y-5">
            <h3 class="text-base font-heading font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xs mr-2.5">3</span>
                    Staff Document & Certificate Attachment Space
                </div>
                <span class="text-[10px] font-extrabold text-teal-800 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">Document Vault</span>
            </h3>

            <p class="text-xs text-slate-500 font-medium">Attach staff certificates, medical licenses, degree diplomas, employment contracts, or ID proofs.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Document Title</label>
                    <input type="text" name="document_title" value="{{ old('document_title') }}" placeholder="e.g. MCI Medical License Certificate" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-700 font-semibold text-slate-900" />
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Upload Document File (PDF / Image)</label>
                    <input type="file" name="document_file" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-700 font-semibold" />
                </div>
            </div>
        </div>

        <!-- Section 4: Module Access Permissions Control -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-card space-y-5">
            <h3 class="text-base font-heading font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xs mr-2.5">4</span>
                Module Access Permissions & Control
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold text-slate-700">
                <!-- Appointments -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <span class="font-extrabold text-slate-900 text-xs block">📅 Appointments & Scheduling</span>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>View Clinic Schedule</span>
                        <input type="checkbox" name="permissions[]" value="appointments.view" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>Book & Walk-In Registration</span>
                        <input type="checkbox" name="permissions[]" value="appointments.create" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>Cancel & Reschedule</span>
                        <input type="checkbox" name="permissions[]" value="appointments.cancel" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                </div>

                <!-- Prescriptions -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <span class="font-extrabold text-slate-900 text-xs block">💊 Prescriptions & Rx</span>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>View Active Prescriptions</span>
                        <input type="checkbox" name="permissions[]" value="prescriptions.view" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>Create & Sign Prescriptions</span>
                        <input type="checkbox" name="permissions[]" value="prescriptions.create" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                </div>

                <!-- EHR -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <span class="font-extrabold text-slate-900 text-xs block">🩺 Electronic Health Records (EHR)</span>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>View Patient Medical History</span>
                        <input type="checkbox" name="permissions[]" value="patients.view" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>Update Vitals & Clinical Notes</span>
                        <input type="checkbox" name="permissions[]" value="patients.edit" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                </div>

                <!-- Billing -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <span class="font-extrabold text-slate-900 text-xs block">💳 Invoices & Billing</span>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>View Billing & Invoices</span>
                        <input type="checkbox" name="permissions[]" value="invoices.view" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span>Record & Collect Payments</span>
                        <input type="checkbox" name="permissions[]" value="payments.manage" checked class="rounded text-teal-800 focus:ring-teal-700">
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Submit Buttons -->
        <div class="pt-2 flex items-center justify-end space-x-4">
            <a href="{{ route('subaccounts.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">Cancel</a>
            <button type="submit" class="px-8 py-3 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-teal-900/20 transition-all transform hover:-translate-y-0.5">
                Create Staff Account →
            </button>
        </div>
    </form>
</div>
@endsection
