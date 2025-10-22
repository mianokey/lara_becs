<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

public function toggle()
{
    $user = Auth::user();

    if ($user->is_clocked_in) {
        $record = $user->attendances()->latest()->first();
        $record->update(['time_out' => now()]);
        $user->update(['is_clocked_in' => false]);
    } else {
        $user->attendances()->create([
            'date' => now()->toDateString(),
            'time_in' => now(),
        ]);
        $user->update(['is_clocked_in' => true]);
    }

    return back()->with('status', $user->is_clocked_in ? 'Clocked In' : 'Clocked Out');
}
}