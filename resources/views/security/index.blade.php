@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'audit' }">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold rounded-2xl text-xs flex items-center justify-between shadow-sm">
            <span>✓ {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold ml-4">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 font-extrabold rounded-2xl text-xs flex items-center justify-between shadow-sm">
            <span>⚠️ {{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold ml-4">&times;</button>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>System Administration</span>
                <span>/</span>
                <span class="text-teal-800">Security, Audit Trail & Backups</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Security & Audit Logs</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Track staff activity audit trail with IP addresses and create 1-click database backups.</p>
        </div>

        <form method="POST" action="{{ route('security.backup.create') }}">
            @csrf
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center space-x-1.5">
                <span>💾 Create 1-Click Database Backup</span>
            </button>
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 space-x-6 text-xs font-black uppercase tracking-wider">
        <button @click="tab = 'audit'" :class="tab === 'audit' ? 'border-b-2 border-teal-700 text-teal-800 pb-3' : 'text-slate-400 pb-3 hover:text-slate-700'">
            🛡️ Staff Activity Audit Trail
        </button>
        <button @click="tab = 'backups'" :class="tab === 'backups' ? 'border-b-2 border-teal-700 text-teal-800 pb-3' : 'text-slate-400 pb-3 hover:text-slate-700'">
            💾 Database Backup Archives
        </button>
    </div>

    <!-- Tab 1: Staff Activity Audit Trail -->
    <div x-show="tab === 'audit'" class="space-y-4">
        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-card">
            <form method="GET" action="{{ route('security.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="relative w-full sm:w-96">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by staff name, action, IP..." class="w-full pl-10 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium" />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <div class="flex items-center space-x-3">
                    <select name="module" onchange="this.form.submit()" class="px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold">
                        <option value="">All Modules</option>
                        <option value="Invoices" {{ request('module') === 'Invoices' ? 'selected' : '' }}>Invoices</option>
                        <option value="Patients" {{ request('module') === 'Patients' ? 'selected' : '' }}>Patients</option>
                        <option value="Prescriptions" {{ request('module') === 'Prescriptions' ? 'selected' : '' }}>Prescriptions</option>
                        <option value="Appointments" {{ request('module') === 'Appointments' ? 'selected' : '' }}>Appointments</option>
                        <option value="Duty Roster" {{ request('module') === 'Duty Roster' ? 'selected' : '' }}>Duty Roster</option>
                        <option value="Security & Backups" {{ request('module') === 'Security & Backups' ? 'selected' : '' }}>Security & Backups</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl">Filter</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                            <th class="p-3.5">Timestamp</th>
                            <th class="p-3.5">Staff Member / User</th>
                            <th class="p-3.5">Role</th>
                            <th class="p-3.5">Action Executed</th>
                            <th class="p-3.5">Module</th>
                            <th class="p-3.5">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($auditLogs as $log)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3.5 font-bold text-slate-900">{{ $log->created_at }}</td>
                                <td class="p-3.5 font-extrabold text-teal-800">{{ $log->user_name }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-700 uppercase">
                                        {{ $log->user_role }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-800 font-semibold">{{ $log->action }}</td>
                                <td class="p-3.5 font-bold text-slate-600">{{ $log->module }}</td>
                                <td class="p-3.5 font-mono text-[11px] text-slate-500">{{ $log->ip_address ?: '127.0.0.1' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-400 font-medium">No audit logs recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $auditLogs->links() }}
            </div>
        </div>
    </div>

    <!-- Tab 2: Database Backup Archives -->
    <div x-show="tab === 'backups'" class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-heading font-black text-slate-900 text-sm">Database Backup Archives</h3>
                <p class="text-xs text-slate-400 font-semibold">1-Click database SQL dump creation and restoration archives</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="p-3.5">Filename</th>
                        <th class="p-3.5">Creation Date</th>
                        <th class="p-3.5">File Size</th>
                        <th class="p-3.5">Type</th>
                        <th class="p-3.5">Created By</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($backups as $b)
                        <tr>
                            <td class="p-3.5 font-bold text-slate-900 font-mono">{{ $b->filename }}</td>
                            <td class="p-3.5 text-slate-600">{{ $b->created_at }}</td>
                            <td class="p-3.5 font-bold text-teal-800">{{ round($b->file_size_bytes / 1024, 2) }} KB</td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200 uppercase">
                                    {{ $b->backup_type }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-700">{{ $b->creator->name ?? 'System Admin' }}</td>
                            <td class="p-3.5 text-right space-x-2">
                                <a href="{{ route('security.backup.download', $b->id) }}" class="px-3 py-1 bg-teal-800 hover:bg-teal-900 text-white font-bold text-[11px] rounded-lg shadow-sm">
                                    ⬇️ Download SQL
                                </a>
                                <form method="POST" action="{{ route('security.backup.destroy', $b->id) }}" class="inline-block" onsubmit="return confirm('Delete backup file?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold text-[11px] rounded-lg">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400 font-medium">No database backups generated yet. Click "Create 1-Click Database Backup" at top.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
