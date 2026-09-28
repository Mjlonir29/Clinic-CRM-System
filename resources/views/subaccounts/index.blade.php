@extends('layouts.app')

@section('content')
@php
    $initialUser = $users->first();
    $defaultPermissions = [
        'appointments.view', 'appointments.create', 'appointments.cancel',
        'prescriptions.view', 'prescriptions.create',
        'patients.view', 'patients.edit',
        'invoices.view', 'payments.manage'
    ];
@endphp

<div class="space-y-6" x-data="{ 
    selectedStaff: {
        id: {{ $initialUser->id ?? 0 }},
        name: '{{ addslashes($initialUser->name ?? "") }}',
        username: '{{ addslashes($initialUser->username ?? "") }}',
        email: '{{ addslashes($initialUser->email ?? "") }}',
        phone: '{{ addslashes($initialUser->phone ?? "") }}',
        role_id: {{ $initialUser->role_id ?? 1 }},
        role_name: '{{ addslashes($initialUser->role->name ?? "Admin") }}',
        status: '{{ $initialUser->status ?? "active" }}',
        qualifications: '{{ addslashes($initialUser->qualifications ?? "") }}',
        specialization: '{{ addslashes($initialUser->specialization ?? "") }}',
        registration_number: '{{ addslashes($initialUser->registration_number ?? "") }}',
        cabin_number: '{{ addslashes($initialUser->cabin_number ?? "") }}',
        working_hours: '{{ addslashes($initialUser->working_hours ?? "") }}',
        permissions: {{ json_encode($initialUser->permissions ?? $defaultPermissions) }},
        documents: {{ json_encode($initialUser->documents ?? []) }}
    },

    hasPerm(perm) {
        if (!this.selectedStaff.permissions) return false;
        return this.selectedStaff.permissions.includes(perm) || this.selectedStaff.permissions.includes('*');
    },

    togglePerm(perm) {
        if (!this.selectedStaff.permissions) {
            this.selectedStaff.permissions = [];
        }
        if (this.hasPerm(perm)) {
            this.selectedStaff.permissions = this.selectedStaff.permissions.filter(p => p !== perm && p !== '*');
        } else {
            this.selectedStaff.permissions.push(perm);
        }
    }
}">

    <!-- Header Breadcrumbs & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinic Management</span>
                <span>/</span>
                <span class="text-teal-800">Staff Accounts</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Staff Directory & Management</h1>
                <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-teal-50 text-teal-800 border border-teal-200/60 rounded-md">{{ count($users) }} Active Accounts</span>
            </div>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Manage staff logins, assign roles, reset passwords, upload documents & adjust module permissions.</p>
        </div>

        <div>
            <a href="{{ route('subaccounts.create') }}" class="px-4 py-2.5 bg-teal-800 hover:bg-teal-900 active:bg-teal-950 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-teal-900/20 transition-all flex items-center transform hover:-translate-y-0.5">
                <span class="text-base mr-1.5">+</span>
                <span>Add Staff Member</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Split Layout: Staff List (Left 7 Cols) & Permissions Control (Right 5 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left 7 Cols: Staff Directory Table -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-black text-base text-slate-900">Clinic Staff Members</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Click a staff member to view, edit credentials, reset password & manage document uploads</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400">{{ count($users) }} Staff</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-black uppercase text-slate-400">
                                <th class="py-3 px-4">Staff Member</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold">
                            @foreach($users as $user)
                                @php
                                    $userPerms = $user->permissions ?? ($user->role->permissions ?? $defaultPermissions);
                                @endphp
                                <tr 
                                    @click="selectedStaff = {
                                        id: {{ $user->id }},
                                        name: '{{ addslashes($user->name) }}',
                                        username: '{{ addslashes($user->username ?? "") }}',
                                        email: '{{ addslashes($user->email) }}',
                                        phone: '{{ addslashes($user->phone ?? "") }}',
                                        role_id: {{ $user->role_id ?? 1 }},
                                        role_name: '{{ addslashes($user->role->name ?? ucfirst($user->role_slug)) }}',
                                        status: '{{ $user->status }}',
                                        qualifications: '{{ addslashes($user->qualifications ?? "") }}',
                                        specialization: '{{ addslashes($user->specialization ?? "") }}',
                                        registration_number: '{{ addslashes($user->registration_number ?? "") }}',
                                        cabin_number: '{{ addslashes($user->cabin_number ?? "") }}',
                                        working_hours: '{{ addslashes($user->working_hours ?? "") }}',
                                        permissions: {{ json_encode($userPerms) }},
                                        documents: {{ json_encode($user->documents) }}
                                    }"
                                    :class="selectedStaff.id === {{ $user->id }} ? 'bg-teal-50/70 ring-1 ring-teal-200' : 'hover:bg-slate-50/80'"
                                    class="cursor-pointer transition-colors"
                                >
                                    <!-- Staff Member Info -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-9 h-9 rounded-2xl bg-teal-800 text-white font-extrabold flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-heading font-extrabold text-slate-900 text-xs">{{ $user->name }}</p>
                                                <p class="text-[10px] text-slate-500 font-medium">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role -->
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $user->role->name ?? ucfirst($user->role_slug) }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3.5 px-4">
                                        @if($user->status === 'active')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                ● Active
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action -->
                                    <td class="py-3.5 px-4 text-right">
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('subaccounts.destroy', $user->id) }}" onsubmit="return confirm('Are you sure you want to delete this staff account?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] font-bold text-slate-400 italic">Current User</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right 5 Cols: Credentials, Documents & Permissions Controls -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-card space-y-6 flex flex-col justify-between">
            <div class="space-y-5">
                <!-- Update Account & Permissions Form -->
                <form method="POST" :action="'/subaccounts/' + selectedStaff.id" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Header for Selected Staff -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">ACCESS PERMISSIONS & PROFILE</span>
                            <h3 class="font-heading font-black text-base text-slate-900" x-text="selectedStaff.name"></h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-teal-50 text-teal-800 border border-teal-200" x-text="selectedStaff.role_name"></span>
                    </div>

                    <!-- Account Editable Profile Fields -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 text-xs">
                        <span class="font-extrabold text-slate-900 text-xs block border-b border-slate-200/60 pb-1">⚙️ Account Credentials & Password</span>
                        
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Staff Name</label>
                                <input type="text" name="name" x-model="selectedStaff.name" required class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg font-semibold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Username</label>
                                <input type="text" name="username" x-model="selectedStaff.username" required class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg font-semibold text-slate-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Email</label>
                                <input type="email" name="email" x-model="selectedStaff.email" required class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg font-semibold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Role</label>
                                <select name="role_id" x-model="selectedStaff.role_id" required class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg font-bold text-slate-900">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Reset Password (Optional)</label>
                                <input type="password" name="password" placeholder="New Password..." class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg font-semibold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Account Status</label>
                                <select name="status" x-model="selectedStaff.status" required class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg font-bold text-slate-900">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Permission Toggles List -->
                    <div class="space-y-3 text-xs font-semibold text-slate-700">
                        
                        <!-- Appointments -->
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <span class="font-extrabold text-slate-900 text-xs block">📅 Appointments & Scheduling</span>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>View Clinic Schedule</span>
                                <input type="checkbox" name="permissions[]" value="appointments.view" :checked="hasPerm('appointments.view')" @change="togglePerm('appointments.view')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>Book & Walk-In Registration</span>
                                <input type="checkbox" name="permissions[]" value="appointments.create" :checked="hasPerm('appointments.create')" @change="togglePerm('appointments.create')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>Cancel & Reschedule</span>
                                <input type="checkbox" name="permissions[]" value="appointments.cancel" :checked="hasPerm('appointments.cancel')" @change="togglePerm('appointments.cancel')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                        </div>

                        <!-- Prescriptions -->
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <span class="font-extrabold text-slate-900 text-xs block">💊 Prescriptions & Rx</span>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>View Active Prescriptions</span>
                                <input type="checkbox" name="permissions[]" value="prescriptions.view" :checked="hasPerm('prescriptions.view')" @change="togglePerm('prescriptions.view')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>Create & Sign Prescriptions</span>
                                <input type="checkbox" name="permissions[]" value="prescriptions.create" :checked="hasPerm('prescriptions.create')" @change="togglePerm('prescriptions.create')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                        </div>

                        <!-- Health Records (EHR) -->
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <span class="font-extrabold text-slate-900 text-xs block">🩺 Electronic Health Records (EHR)</span>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>View Patient Medical History</span>
                                <input type="checkbox" name="permissions[]" value="patients.view" :checked="hasPerm('patients.view')" @change="togglePerm('patients.view')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>Update Vitals & Clinical Notes</span>
                                <input type="checkbox" name="permissions[]" value="patients.edit" :checked="hasPerm('patients.edit')" @change="togglePerm('patients.edit')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                        </div>

                        <!-- Billing & Invoices -->
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <span class="font-extrabold text-slate-900 text-xs block">💳 Invoices & Billing</span>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>View Billing & Invoices</span>
                                <input type="checkbox" name="permissions[]" value="invoices.view" :checked="hasPerm('invoices.view')" @change="togglePerm('invoices.view')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span>Record & Collect Payments</span>
                                <input type="checkbox" name="permissions[]" value="payments.manage" :checked="hasPerm('payments.manage')" @change="togglePerm('payments.manage')" class="rounded text-teal-800 focus:ring-teal-700">
                            </label>
                        </div>

                    </div>

                    <!-- Save Action Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-teal-800 hover:bg-teal-900 active:bg-teal-950 text-white font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer">
                            Save Permissions for <span x-text="selectedStaff.name"></span> →
                        </button>
                    </div>
                </form>

                <!-- Staff Specific Document Storage Space -->
                <div class="p-4 rounded-2xl bg-teal-50/60 border border-teal-200/80 space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-heading font-black text-slate-900 text-xs flex items-center">
                            <span class="mr-1.5">📁</span>
                            Staff Documents & Certificates Space
                        </span>
                        <span class="text-[10px] font-bold text-teal-800" x-text="(selectedStaff.documents ? selectedStaff.documents.length : 0) + ' Documents'"></span>
                    </div>

                    <!-- Upload Form -->
                    <form method="POST" :action="'/subaccounts/' + selectedStaff.id + '/documents'" enctype="multipart/form-data" class="space-y-2 pt-1 border-t border-teal-100">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="title" required placeholder="Document Title (e.g. License)..." class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold">
                            <input type="file" name="document" required class="w-full px-2 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-semibold">
                        </div>
                        <button type="submit" class="w-full py-2 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-sm">
                            + Upload Document to Vault
                        </button>
                    </form>

                    <!-- Document Files List -->
                    <div class="space-y-1.5 pt-2 border-t border-teal-100 max-h-40 overflow-y-auto">
                        <template x-if="selectedStaff.documents && selectedStaff.documents.length > 0">
                            <div>
                                <template x-for="doc in selectedStaff.documents" :key="doc.id">
                                    <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-teal-100/80">
                                        <div class="flex items-center space-x-2 truncate">
                                            <span class="text-xs">📄</span>
                                            <span class="font-bold text-slate-900 truncate" x-text="doc.title"></span>
                                        </div>
                                        <div class="flex items-center space-x-2 shrink-0">
                                            <a :href="doc.file_path" target="_blank" class="text-teal-800 hover:underline font-extrabold text-[10px]">View ↗</a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!selectedStaff.documents || selectedStaff.documents.length === 0">
                            <p class="text-[11px] text-slate-400 italic text-center py-2">No certificates or documents attached yet.</p>
                        </template>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection
