<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

public function index()
{
    $user = Auth::user();
    $userId = $user->id;

    $ahpProjects = Project::where('type', 'AHP')->get();
    $privateProjects = Project::where('type', 'Private')->get();

    $taskQuery = Task::where('is_weekly_deliverable', true);

    if (!$user->hasAnyRole(['admin', 'director'])) {
        $taskQuery->where('assignee_id', $userId);
    }

    $incompleteTasks = (clone $taskQuery)
        ->whereIn('status', ['not_started', 'pending','review_task','in_progress'])
        ->orderBy('target_completion_date', 'asc')
        ->get();

    $completedTasks = (clone $taskQuery)
        ->where('status', 'completed')
        ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->orderBy('updated_at', 'desc')
        ->get();

    $viewData = [
        'user' => $user,
        'ahpProjects' => $ahpProjects,
        'privateProjects' => $privateProjects,
        'incompleteTasks' => $incompleteTasks,
        'completedTasks' => $completedTasks,
    ];

    if ($user->hasAnyRole(['admin', 'director'])) {
        // Active Projects
        $viewData['activeProjectsCount'] = Project::where('status', 'active')->count();

        // Weekly Tasks
        $viewData['weeklyTasksCount'] = Task::where('is_weekly_deliverable', true)->count();

        // Staff Attendance (placeholder for now)
        $viewData['staffAttendanceCount'] = 13;

        // Online Staff Count
        $cutoff = now()->subMinutes(10)->timestamp; // active within last 10 minutes

        $onlineCount = DB::table('sessions')
            ->where('last_activity', '>=', $cutoff)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $viewData['onlineStaffCount'] = $onlineCount;
    }

    $view = $user->hasAnyRole(['admin', 'director'])
        ? 'dashboard.executive'
        : 'dashboard.user';

    return view($view, $viewData);
}
}