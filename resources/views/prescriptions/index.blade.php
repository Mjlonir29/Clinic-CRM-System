@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Breadcrumbs & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinical EHR</span>
                <span>/</span>
                <span class="text-teal-800">Prescription Registry</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Prescriptions Directory & EHR History</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Digital Rx records, standardized medication histories & printable scripts</p>
        </div>

        <a href="{{ route('prescriptions.create') }}" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/20 transition-all flex items-center transform hover:-translate-y-0.5 self-start sm:self-auto">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Prescription
        </a>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-card">
        <form method="GET" action="{{ route('prescriptions.index') }}" class="flex items-center space-x-3">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search RX #, Patient Name, Diagnosis..." 
                class="px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 w-full sm:w-80 font-medium"
            />
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition-all">Search</button>
            @if(request('search'))
                <a href="{{ route('prescriptions.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Clear</a>
            @endif
        </form>
    </div>

    <!-- Prescriptions Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">RX Serial ID</th>
                        <th class="py-3.5 px-5">Patient Name</th>
                        <th class="py-3.5 px-5">Prescription Date</th>
                        <th class="py-3.5 px-5">Diagnosis</th>
                        <th class="py-3.5 px-5">Formulated Meds</th>
                        <th class="py-3.5 px-5">Follow-up</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($prescriptions as $rx)
                        <tr class="hover:bg-teal-50/30 transition-colors">
                            <td class="py-4 px-5">
                                <span class="font-extrabold text-teal-800 bg-teal-50 border border-teal-200/60 px-2 py-0.5 rounded">
                                    {{ $rx->prescription_number }}
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <a href="{{ route('patients.show', $rx->patient_id) }}" class="font-heading font-black text-slate-900 hover:text-teal-800 block">
                                    {{ $rx->patient->full_name }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ $rx->patient->patient_id }}</span>
                            </td>
                            <td class="py-4 px-5 font-bold text-slate-800">{{ $rx->prescription_date }}</td>
                            <td class="py-4 px-5 font-extrabold text-slate-900">{{ $rx->diagnosis }}</td>
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 font-extrabold rounded-lg text-[10px] border border-emerald-200/60">
                                    💊 {{ count($rx->items) }} Medications
                                </span>
                            </td>
                            <td class="py-4 px-5 text-slate-600 font-medium">
                                {{ $rx->follow_up_date ?: 'None scheduled' }}
                            </td>
                            <td class="py-4 px-5 text-right space-x-2">
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
                        <tr><td colspan="7" class="p-8 text-center text-slate-400 font-medium">No prescriptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prescriptions->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
