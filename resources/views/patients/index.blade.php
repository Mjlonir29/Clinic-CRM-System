@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'all' }">

    <!-- Header Breadcrumbs & Action Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinical Administration</span>
                <span>/</span>
                <span class="text-teal-800">Master Patient Index (MPI)</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Patients Directory & EHR Registry</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Centralized Electronic Health Record (EHR) registry, demographics & medical histories</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('patients.create') }}" class="px-4 py-2.5 bg-teal-800 hover:bg-teal-900 active:bg-teal-950 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-teal-900/20 transition-all flex items-center transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span>Register New Patient</span>
            </a>
        </div>
    </div>

    <!-- Filter Pills & Search Toolbar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-card space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <!-- Category Tabs -->
            <div class="flex items-center bg-slate-100 p-1 rounded-2xl text-xs font-extrabold space-x-1">
                <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-xl transition-all">All Records ({{ $patients->total() }})</button>
                <button @click="tab = 'active'" :class="tab === 'active' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-xl transition-all">Active</button>
                <button @click="tab = 'high_risk'" :class="tab === 'high_risk' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-xl transition-all">High Risk</button>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('patients.index') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by Patient Name, MRN #, Phone..." 
                    class="px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 w-full sm:w-72 font-medium"
                />
                
                <select name="gender" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium">
                    <option value="">All Genders</option>
                    <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl transition-all">Search</button>
                @if(request()->hasAny(['search', 'gender']))
                    <a href="{{ route('patients.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Clear</a>
                @endif
            </form>

        </div>
    </div>

    <!-- MPI Master Patient Directory Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Patient Name / MRN</th>
                        <th class="py-3.5 px-5">Age / Gender</th>
                        <th class="py-3.5 px-5">Blood Group</th>
                        <th class="py-3.5 px-5">Phone & Email</th>
                        <th class="py-3.5 px-5">Condition & Allergies</th>
                        <th class="py-3.5 px-5">Last Consultation</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($patients as $pt)
                        <tr 
                            x-show="
                                tab === 'all' ||
                                (tab === 'active' && '{{ $pt->status }}' === 'Active') ||
                                (tab === 'high_risk' && ('{{ $pt->allergies }}' !== '' || {{ $pt->age ?? 0 }} >= 60))
                            "
                            class="hover:bg-teal-50/30 transition-colors"
                        >
                            
                            <!-- Patient Name & MRN -->
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-teal-800 text-white font-extrabold flex items-center justify-center text-xs flex-shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($pt->first_name, 0, 1) . substr($pt->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('patients.show', $pt->id) }}" class="font-heading font-black text-sm text-slate-900 hover:text-teal-800 block">
                                            {{ $pt->full_name }}
                                        </a>
                                        <span class="text-[10px] font-extrabold text-teal-800 bg-teal-50 border border-teal-200/60 px-1.5 py-0.5 rounded">
                                            MRN #{{ $pt->patient_id }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Age & Gender -->
                            <td class="py-4 px-5 font-semibold text-slate-800">
                                {{ $pt->age ?? 'N/A' }} yrs
                                <span class="block text-[10px] text-slate-400 font-bold uppercase">{{ $pt->gender }}</span>
                            </td>

                            <!-- Blood Group Badge -->
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-rose-50 text-rose-800 border border-rose-200/70">
                                    🩸 {{ $pt->blood_group ?? 'O+' }}
                                </span>
                            </td>

                            <!-- Phone & Email -->
                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-900 block">{{ $pt->phone }}</span>
                                <span class="text-[11px] text-slate-400 font-medium block">{{ $pt->email ?? 'No email on record' }}</span>
                            </td>

                            <!-- Condition & Allergies -->
                            <td class="py-4 px-5">
                                <div class="flex flex-wrap gap-1">
                                    @if($pt->existing_conditions)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-800">{{ $pt->existing_conditions }}</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-teal-50 text-teal-800">General OPD</span>
                                    @endif
                                    @if($pt->allergies)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">⚠️ {{ $pt->allergies }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Last Visit -->
                            <td class="py-4 px-5 text-slate-600 font-medium">
                                @php $lv = $pt->lastVisit(); @endphp
                                @if($lv)
                                    <span class="font-bold text-slate-900 block">{{ \Carbon\Carbon::parse($lv->appointment_date)->format('M d, Y') }}</span>
                                    <span class="text-[10px] text-teal-800 font-semibold block">{{ $lv->appointment_type }}</span>
                                @else
                                    <span class="text-slate-400 italic">No previous visit</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('patients.show', $pt->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-[11px] rounded-xl transition-all">
                                        View Chart
                                    </a>
                                    <a href="{{ route('prescriptions.create', ['patient_id' => $pt->id]) }}" class="px-3 py-1.5 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-[11px] rounded-xl shadow-2xs transition-all">
                                        Create Rx
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 font-medium">
                                No patient records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $patients->links() }}
        </div>
    </div>

</div>
@endsection
