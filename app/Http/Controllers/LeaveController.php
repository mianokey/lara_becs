<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaveType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;

class LeaveController extends Controller
{
    public function adjustForm()
    {
        $users = User::all();
        return view('leave.adjust', compact('users'));
    }

    public function edit(User $user)
    {
        $leaveTypes = LeaveType::all();
        $userBalances = LeaveBalance::where('user_id', $user->id)
            ->where('year', date('Y'))
            ->pluck('balance_days', 'leave_type_id')
            ->toArray();

        return view('leave.edit', compact('user', 'leaveTypes', 'userBalances'));
    }

    public function adjustUpdate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'balances' => 'required|array',
            'balances.*' => 'required|integer|min:0',
        ]);

        foreach ($request->balances as $leaveTypeId => $balance) {
            LeaveBalance::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'leave_type_id' => $leaveTypeId,
                    'year' => date('Y'),
                ],
                [
                    'total_days' => $balance,
                    'used_days' => 0,
                    'carried_forward' => 0,
                    'balance_days' => $balance,
                ]
            );
        }

        return redirect()->route('leave_balances.index')->with('success', 'Leave balances updated successfully.');
    }

    public function apply()
    {
        $balances = LeaveBalance::with('leaveType')
            ->where('user_id', auth()->id())
            ->get();

        $leaveTypes = LeaveType::all();

        return view('leave.apply', compact('balances', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $userId = auth()->id();
        $leaveTypeId = $request->leave_type_id;

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // ----------------------------------------------
        // 1️⃣ Check for overlapping leaves
        // ----------------------------------------------
        $overlap = LeaveApplication::where('user_id', $userId)
            ->where('status', '!=', 'rejected') // ignore rejected leaves
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'dates' => 'You already have a leave that overlaps with these dates. Please choose different dates.'
            ]);
        }

        // ----------------------------------------------
        // 2️⃣ Get Kenyan Public Holidays Dynamically
        // ----------------------------------------------
        $year = $start->year;
        $holidayUrl = "https://date.nager.at/api/v3/PublicHolidays/{$year}/KE";
        $holidayResponse = Http::get($holidayUrl);

        $holidays = $holidayResponse->successful()
            ? collect($holidayResponse->json())->pluck('date')
            : collect();

        // ----------------------------------------------
        // 3️⃣ Calculate leave days excluding weekends + holidays
        // ----------------------------------------------
        $period = CarbonPeriod::create($start, $end);

        $daysRequested = collect($period)->filter(function ($date) use ($holidays) {
            if ($date->isWeekend()) return false;
            if ($holidays->contains($date->format('Y-m-d'))) return false;
            return true;
        })->count();

        // ----------------------------------------------
        // 4️⃣ Check leave balance
        // ----------------------------------------------
        $balance = LeaveBalance::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', now()->year)
            ->first();

        if (!$balance || $balance->balance_days < $daysRequested) {
            return back()->withErrors([
                "You need {$daysRequested} days but only {$balance->balance_days} days are available."
            ]);
        }

        // ----------------------------------------------
        // 5️⃣ Compute return date
        // ----------------------------------------------
        $returnDate = $end->copy()->addDay();
        while ($returnDate->isWeekend() || $holidays->contains($returnDate->format('Y-m-d'))) {
            $returnDate->addDay();
        }

        // ----------------------------------------------
        // 6️⃣ Store leave application
        // ----------------------------------------------
        LeaveApplication::create([
            'user_id'       => $userId,
            'leave_type_id' => $leaveTypeId,
            'start_date'    => $start->toDateString(),
            'end_date'      => $end->toDateString(),
            'days_requested' => $daysRequested,
            'return_date'   => $returnDate->toDateString(),
            'reason'        => $request->reason,
            'status'        => 'pending',
        ]);

        // ----------------------------------------------
        // 7️⃣ Update leave balance
        // ----------------------------------------------
        $balance->used_days += $daysRequested;
        $balance->balance_days -= $daysRequested;
        $balance->save();

        return redirect()->route('leaves.history')
            ->with('success', "Leave applied successfully. Return to work on {$returnDate->toFormattedDateString()}");
    }

    public function history()
{
    $leaves = LeaveApplication::with('leaveType')
        ->where('user_id', auth()->id())
        ->orderBy('start_date', 'desc')
        ->get();

    return view('leave.history', compact('leaves'));
}

public function manageUpdate(Request $request, LeaveApplication $leave)
{
    $request->validate([
        'status' => 'required|in:approved,rejected',
        'decline_reason' => 'nullable|string|max:500',
    ]);

    if ($request->status === 'rejected' && empty($request->decline_reason)) {
        return back()->withErrors(['decline_reason' => 'Please provide a reason for declining the leave.']);
    }

    $leave->status = $request->status;
    if ($request->status === 'rejected') {
        $leave->remarks = $request->decline_reason;
    }
    $leave->approved_by = auth()->id();
    $leave->approved_at = now();
    $leave->save();

    return back()->with('success', 'Leave updated successfully.');
}


}
