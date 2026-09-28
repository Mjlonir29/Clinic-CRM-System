@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'schedules', leaveModalOpen: false, shiftModalOpen: false }">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold rounded-2xl text-xs flex items-center justify-between shadow-sm">
            <span>✓ {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold ml-4">&times;</button>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinical Operations</span>
                <span>/</span>
                <span class="text-teal-800">Staff Duty Roster & Scheduling</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Staff Duty Roster & Doctor Availability</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Manage doctor working hours, shift assignments, and leave applications.</p>
        </div>

        <div class="flex items-center space-x-3">
            <button @click="leaveModalOpen = true" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                + Apply Doctor Leave
            </button>
            <button @click="shiftModalOpen = true" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                + Assign Staff Shift
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 space-x-6 text-xs font-black uppercase tracking-wider">
        <button @click="tab = 'schedules'" :class="tab === 'schedules' ? 'border-b-2 border-teal-700 text-teal-800 pb-3' : 'text-slate-400 pb-3 hover:text-slate-700'">
            🩺 Doctor Availability Schedules
        </button>
        <button @click="tab = 'roster'" :class="tab === 'roster' ? 'border-b-2 border-teal-700 text-teal-800 pb-3' : 'text-slate-400 pb-3 hover:text-slate-700'">
            📅 Staff Shift Roster
        </button>
        <button @click="tab = 'leaves'" :class="tab === 'leaves' ? 'border-b-2 border-teal-700 text-teal-800 pb-3' : 'text-slate-400 pb-3 hover:text-slate-700'">
            🏖️ Doctor Leave Applications
        </button>
    </div>

    <!-- Tab 1: Doctor Availability Schedules -->
    <div x-show="tab === 'schedules'" class="space-y-6">
        @foreach($doctors as $doc)
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-800 text-white font-black flex items-center justify-center text-sm">
                            {{ strtoupper(substr($doc->first_name ?? $doc->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-heading font-black text-slate-900 text-sm">{{ $doc->name }}</h3>
                            <span class="text-xs font-semibold text-teal-700">{{ $doc->specialization ?? 'General Physician' }}</span>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-800 text-[10px] font-extrabold rounded-md border border-emerald-200">Active Duty</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @php $docScheds = isset($schedules[$doc->id]) ? $schedules[$doc->id] : collect(); @endphp
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                        @php $sched = $docScheds->firstWhere('day_of_week', $day); @endphp
                        <form method="POST" action="{{ route('roster.schedule.update') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            @csrf
                            <input type="hidden" name="doctor_id" value="{{ $doc->id }}">
                            <input type="hidden" name="day_of_week" value="{{ $day }}">
                            
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">{{ $day }}</span>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_available" value="1" {{ ($sched && $sched->is_available) ? 'checked' : '' }} class="sr-only peer" onchange="this.form.submit()">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal-700"></div>
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div>
                                    <label class="block text-slate-400 font-semibold mb-0.5">Start Time</label>
                                    <input type="time" name="start_time" value="{{ $sched ? substr($sched->start_time, 0, 5) : '09:00' }}" class="w-full px-2 py-1 bg-white border border-slate-200 rounded-lg font-bold" />
                                </div>
                                <div>
                                    <label class="block text-slate-400 font-semibold mb-0.5">End Time</label>
                                    <input type="time" name="end_time" value="{{ $sched ? substr($sched->end_time, 0, 5) : '17:00' }}" class="w-full px-2 py-1 bg-white border border-slate-200 rounded-lg font-bold" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div>
                                    <label class="block text-slate-400 font-semibold mb-0.5">Break Start</label>
                                    <input type="time" name="break_start" value="{{ $sched ? substr($sched->break_start, 0, 5) : '13:00' }}" class="w-full px-2 py-1 bg-white border border-slate-200 rounded-lg font-bold" />
                                </div>
                                <div>
                                    <label class="block text-slate-400 font-semibold mb-0.5">Break End</label>
                                    <input type="time" name="break_end" value="{{ $sched ? substr($sched->break_end, 0, 5) : '14:00' }}" class="w-full px-2 py-1 bg-white border border-slate-200 rounded-lg font-bold" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[10px] text-slate-500 font-semibold">Slot: 30 Min</span>
                                <input type="hidden" name="slot_duration_minutes" value="30">
                                <button type="submit" class="px-2.5 py-1 bg-slate-900 text-white text-[10px] font-bold rounded-md hover:bg-slate-800">Save</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tab 2: Staff Shift Roster -->
    <div x-show="tab === 'roster'" class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6">
        <h3 class="font-heading font-black text-slate-900 text-sm mb-4">Duty Shift Allocations</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-black uppercase border-b">
                        <th class="p-3">Staff Member</th>
                        <th class="p-3">Shift Date</th>
                        <th class="p-3">Shift Type</th>
                        <th class="p-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($rosters as $r)
                        <tr>
                            <td class="p-3 font-bold text-slate-900">{{ $r->user->name ?? 'Staff' }}</td>
                            <td class="p-3 text-slate-700">{{ $r->shift_date }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase
                                    @if($r->shift_type === 'Morning') bg-amber-100 text-amber-800
                                    @elseif($r->shift_type === 'Evening') bg-blue-100 text-blue-800
                                    @elseif($r->shift_type === 'Night') bg-indigo-100 text-indigo-800
                                    @else bg-slate-100 text-slate-600 @endif
                                ">
                                    {{ $r->shift_type }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-500">{{ $r->notes ?: 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-slate-400 font-medium">No shifts assigned yet. Use top button to add shifts.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 3: Doctor Leave Applications -->
    <div x-show="tab === 'leaves'" class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6">
        <h3 class="font-heading font-black text-slate-900 text-sm mb-4">Approved Doctor Leaves</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-black uppercase border-b">
                        <th class="p-3">Doctor Name</th>
                        <th class="p-3">Leave Date</th>
                        <th class="p-3">Reason</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($leaves as $leave)
                        <tr>
                            <td class="p-3 font-bold text-slate-900">{{ $leave->doctor->name ?? 'Doctor' }}</td>
                            <td class="p-3 font-bold text-rose-700">{{ $leave->leave_date }}</td>
                            <td class="p-3 text-slate-600">{{ $leave->reason ?: 'Personal Leave' }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">
                                    {{ $leave->status }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <form method="POST" action="{{ route('roster.leave.destroy', $leave->id) }}" onsubmit="return confirm('Remove leave entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400 font-medium">No doctor leaves scheduled.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Apply Leave Modal -->
    <div x-show="leaveModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="leaveModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 space-y-4">
                <h3 class="text-base font-extrabold text-slate-900">Record Doctor Leave</h3>
                <form method="POST" action="{{ route('roster.leave.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Select Doctor *</label>
                        <select name="doctor_id" required class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold">
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->specialization ?? 'Doctor' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Leave Date *</label>
                        <input type="date" name="leave_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Reason</label>
                        <input type="text" name="reason" placeholder="e.g. Conference, Medical Leave" class="w-full px-3 py-2 bg-slate-50 border rounded-xl" />
                    </div>

                    <div class="pt-2 flex justify-end space-x-3">
                        <button type="button" @click="leaveModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-amber-600 text-white font-bold rounded-xl shadow-md">Record Leave</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Shift Modal -->
    <div x-show="shiftModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="shiftModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 space-y-4">
                <h3 class="text-base font-extrabold text-slate-900">Assign Staff Duty Shift</h3>
                <form method="POST" action="{{ route('roster.shift.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Select Staff Member *</label>
                        <select name="user_id" required class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold">
                            @foreach($staffMembers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ ucfirst($s->role_slug ?? 'Staff') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Shift Date *</label>
                        <input type="date" name="shift_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Shift Type *</label>
                        <select name="shift_type" required class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold">
                            <option value="Morning">Morning Shift (08:00 - 16:00)</option>
                            <option value="Evening">Evening Shift (16:00 - 24:00)</option>
                            <option value="Night">Night Duty (00:00 - 08:00)</option>
                            <option value="Off">Day Off</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Notes / Instructions</label>
                        <input type="text" name="notes" placeholder="e.g. On-call duty emergency room" class="w-full px-3 py-2 bg-slate-50 border rounded-xl" />
                    </div>

                    <div class="pt-2 flex justify-end space-x-3">
                        <button type="button" @click="shiftModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-teal-800 text-white font-bold rounded-xl shadow-md">Assign Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
