<?php
    namespace App\Http\Controllers;

    use App\Helpers\NotificationHelper;
    use App\Models\Project;
    use App\Models\Task;
    use App\Models\TaskMessage;
    use App\Models\User;
    use App\Notifications\TaskMessageNotification;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;

    class TaskController extends Controller
    {
        public function index()
        {
            $user = Auth::user();

            if ($user->hasAnyRole(['admin', 'director'])) {
                $tasks = Task::with(['assignee', 'project'])->latest()->get();
                $users = User::with(['tasks.project'])->get();
                return view('tasks.index', compact('tasks', 'users', 'user'));
            } else {
                $tasks = Task::with(['assignee', 'project'])
                    ->where('assignee_id', $user->id)
                    ->latest()
                    ->get();
                return view('tasks.index', compact('tasks', 'user'));
            }
        }

        public function create()
        {
            $projects = Project::all();
            $staffUsers = User::role('user')->get();
            $reviewers = User::permission('review tasks')->get();

            return view('tasks.create', compact('projects', 'staffUsers', 'reviewers'));
        }

        public function store(Request $request)
        {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'assignee_id' => 'required|exists:users,id',
                'reviewer_id' => 'nullable|exists:users,id',
                'project_id' => 'required|exists:projects,id',
                'target_completion_date' => 'nullable|date',
            ]);

            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'assignee_id' => $validated['assignee_id'],
                'reviewer_id' => $validated['reviewer_id'] ?? null,
                'project_id' => $validated['project_id'],
                'target_completion_date' => $validated['target_completion_date'] ?? null,
                'created_by' => auth()->id(),
                'status' => 'not_started',
            ]);

            // ✅ Notify the assignee
            $assignee = $task->assignee;
            if ($assignee && $assignee->phone) {
                $msg = "📋 *New Task Assigned*\n\n"
                    . "*Title:* {$task->title}\n"
                    . "*Description:* {$task->description}\n"
                    . "*Due:* {$task->target_completion_date}\n\n"
                    . "Please check your task list.";
                NotificationHelper::sendWhatsApp($assignee->phone, $msg);


                // 🔔 In-app notification
                $message = TaskMessage::create([
                    'task_id' => $task->id,
                    'user_id' => $assignee->id,
                    'message' => str_replace('*', '', $msg),
                ]);

                $assignee->notify(new TaskMessageNotification($message, $assignee->id));
            }

            // ✅ Notify the reviewer
            $reviewer = $task->reviewer;
            if ($reviewer && $reviewer->phone) {
                $msg = "👀 *You’ve been assigned as Reviewer*\n\n"
                    . "*Task:* {$task->title}\n"
                    . "*Assigned To:* {$assignee?->name}\n"
                    . "*Due:* {$task->target_completion_date}\n\n"
                    . "Please monitor progress and approve when ready.";
                NotificationHelper::sendWhatsApp($reviewer->phone, $msg);

                $message = TaskMessage::create([
                    'task_id' => $task->id,
                    'recipient_id' => $reviewer->id,
                    'user_id' => $reviewer->id,
                     'message' => str_replace('*', '', $msg),
                ]);

                $reviewer->notify(new TaskMessageNotification($message, $reviewer->id));
            }

            return redirect()->route('tasks.index')
                ->with('success', 'Task created successfully and notifications sent.');
        }

        public function show(Task $task)
        {
            if (request()->ajax()) {
                $task->load('assignee', 'reviewer', 'project');

                return response()->json([
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'project' => $task->project->name ?? 'N/A',
                    'assignee' => $task->assignee->name ?? 'Unassigned',
                    'reviewer' => $task->reviewer->name ?? 'None',
                    'status' => ucfirst(str_replace('_', ' ', $task->status)),
                    'priority' => ucfirst($task->priority),
                    'target_completion_date' => $task->target_completion_date,
                    'is_weekly_deliverable' => $task->is_weekly_deliverable,
                    'created_at' => $task->created_at->format('M d, Y'),
                    'updated_at' => $task->updated_at->format('M d, Y'),
                ]);
            }

            return view('tasks.show', compact('task'));
        }

public function updateStatus(Request $request, Task $task)
{
    $validated = $request->validate([
        'status' => 'required|in:not_started,in_progress,submitted,review_task,completed'
    ]);

    try {
        $task->update(['status' => $validated['status']]);

        $status = strtoupper(str_replace('_', ' ', $validated['status']));
        $assignee = $task->assignee;
        $reviewer = $task->reviewer;

        //✅Notify the assignee (always)
        if ($assignee && $assignee->phone && $validated['status'] !== 'in_progress') {
            $msg = "🔔 *Task Status Updated*\n\n"
                . "*Title:* {$task->title}\n"
                . "*New Status:* {$status}\n\n"
                . "Keep up the good work!";
            NotificationHelper::sendWhatsApp($assignee->phone, $msg);

            $message = TaskMessage::create([
                'task_id' => $task->id,
                'user_id' => $assignee->id,
                'message' => str_replace('*', '', $msg),
            ]);

            $assignee->notify(new TaskMessageNotification($message, $assignee->id));
        }

        // ✅ Notify the reviewer only when task is submitted for review
        if ($reviewer && $reviewer->phone) {
            if ($validated['status'] === 'submitted') {
                $msg = "📢 *Task Submitted for Review*\n\n"
                    . "*Task:* {$task->title}\n"
                    . "*Status:* {$status}\n"
                    . "*Assigned To:* {$assignee?->name}\n\n"
                    . "Please review and approve when ready.";
                NotificationHelper::sendWhatsApp($reviewer->phone, $msg);

                
            $message = TaskMessage::create([
                'task_id' => $task->id,
                'user_id' => $assignee->id,
                'message' => str_replace('*', '', $msg),
            ]);

            $reviewer->notify(new TaskMessageNotification($message, $reviewer->id));
            }
        }

        // ✅ Notify assignee to review their task
        if ($validated['status'] === 'review_task' && $assignee && $assignee->phone) {
            $msg = "🔄 *Task Ready for Your Review*\n\n"
                . "*Task:* {$task->title}\n"
                . "*Status:* {$status}\n\n"
                . "Please review your task and make any necessary changes.";
            NotificationHelper::sendWhatsApp($assignee->phone, $msg);

            $message = TaskMessage::create([
                'task_id' => $task->id,
                'user_id' => $assignee->id,
                'message' => str_replace('*', '', $msg),
            ]);

            $assignee->notify(new TaskMessageNotification($message, $assignee->id));

        }

        return back()->with('success', 'Task status updated and notifications sent.');
    } catch (\Exception $e) {
        Log::error("Task status update failed: " . $e->getMessage());
        return back()->with('error', 'Failed to update task status.');
    }
}


        public function edit(Task $task)
        {
            $staffUsers = User::role('user')->get();
            $reviewers = User::role(['admin', 'director'])->get();
            $projects = Project::all();

            return view('tasks.edit', compact('task', 'staffUsers', 'reviewers', 'projects'));
        }

        public function update(Request $request, Task $task)
        {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'assignee_id' => 'required|exists:users,id',
                'reviewer_id' => 'nullable|exists:users,id',
                'project_id' => 'required|exists:projects,id',
                'target_completion_date' => 'required|date',
                'priority' => 'required|in:low,medium,high',
                'description' => 'nullable|string',
                'is_weekly_deliverable' => 'nullable|boolean',
            ]);

            $validated['is_weekly_deliverable'] = $request->has('is_weekly_deliverable');

            $task->update($validated);

            return redirect()->route('tasks.edit', $task->id)
                ->with('success', 'Task updated successfully!');
        }

        public function destroy(Task $task)
        {
            $task->delete();
            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
        }
    }
