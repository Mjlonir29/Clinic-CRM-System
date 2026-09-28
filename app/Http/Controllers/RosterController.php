<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use App\Models\StaffRoster;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function index(Request $request)
    {
        $doctors = User::where('role_slug', 'admin')->orWhereNotNull('specialization')->get();
        $staffMembers = User::all();

        // Seed default schedule for doctors if none exists
        foreach ($doctors as $doc) {
            $count = DoctorSchedule::where('doctor_id', $doc->id)->count();
            if ($count === 0) {
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach ($days as $day) {
                    DoctorSchedule::create([
                        'doctor_id' => $doc->id,
                        'day_of_week' => $day,
                        'start_time' => '09:00:00',
                        'end_time' => '17:00:00',
                        'break_start' => '13:00:00',
                        'break_end' => '14:00:00',
                        'slot_duration_minutes' => 30,
                        'is_available' => true,
                    ]);
                }
            }
        }

        $schedules = DoctorSchedule::with('doctor')->get()->groupBy('doctor_id');
        $leaves = DoctorLeave::with(['doctor', 'approver'])->orderBy('leave_date', 'desc')->get();
        $rosters = StaffRoster::with('user')->orderBy('shift_date', 'desc')->get();

        return view('rosters.index', compact('doctors', 'staffMembers', 'schedules', 'leaves', 'rosters'));
    }

    public function updateSchedule(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'break_start' => 'nullable',
            'break_end' => 'nullable',
            'slot_duration_minutes' => 'required|integer|min:10|max:120',
        ]);

        DoctorSchedule::updateOrCreate(
            [
                'doctor_id' => $request->doctor_id,
                'day_of_week' => $request->day_of_week,
            ],
            [
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'break_start' => $request->break_start ?: '13:00:00',
                'break_end' => $request->break_end ?: '14:00:00',
                'slot_duration_minutes' => $request->slot_duration_minutes,
                'is_available' => $request->has('is_available'),
            ]
        );

        AuditLog::record("Updated schedule for Doctor #{$request->doctor_id} ({$request->day_of_week})", "Duty Roster");

        return back()->with('success', 'Doctor working hours & schedule updated.');
    }

    public function storeLeave(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'leave_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        DoctorLeave::create([
            'doctor_id' => $request->doctor_id,
            'leave_date' => $request->leave_date,
            'reason' => $request->reason,
            'status' => 'Approved',
            'approved_by' => auth()->id(),
        ]);

        AuditLog::record("Recorded leave for Doctor #{$request->doctor_id} on {$request->leave_date}", "Duty Roster");

        return back()->with('success', 'Doctor leave recorded successfully.');
    }

    public function storeRoster(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_date' => 'required|date',
            'shift_type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        StaffRoster::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'shift_date' => $request->shift_date,
            ],
            [
                'shift_type' => $request->shift_type,
                'notes' => $request->notes,
            ]
        );

        AuditLog::record("Assigned shift {$request->shift_type} for Staff #{$request->user_id} on {$request->shift_date}", "Duty Roster");

        return back()->with('success', 'Staff roster shift updated.');
    }

    public function deleteLeave(DoctorLeave $leave)
    {
        $leave->delete();
        return back()->with('success', 'Leave record removed.');
    }
}
