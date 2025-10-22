<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskMessage;
use App\Notifications\TaskMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskMessageController extends Controller
{
    /**
     * List all messages for a task
     */
    public function index(Task $task)
    {
        $messages = $task->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'user_id' => $msg->user_id,
                    'user_name' => $msg->user->name,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->toDateTimeString(),
                    'read_at' => $msg->read_at ? true : false,
                ];
            });

        return response()->json($messages);
    }

    /**
     * Store a new message under a task
     */
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $task->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Notify Assignee if not the sender
        if ($task->assignee_id && $task->assignee_id !== Auth::id()) {
            $task->assignee->notify(
                new TaskMessageNotification($message, $task->assignee_id)
            );
        }

        // Notify Reviewer if not the sender
        if ($task->reviewer_id && $task->reviewer_id !== Auth::id()) {
            $task->reviewer->notify(
                new TaskMessageNotification($message, $task->reviewer_id)
            );
        }

        return response()->json(['message' => $message]);
    }

    /**
     * Mark messages of a task as read (for current user)
     */
    public function markAsRead(Task $task)
    {
        $task->messages()
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count
     */
    public function unreadCount()
    {
        $count = TaskMessage::whereNull('read_at')
            ->where('user_id', '!=', Auth::id())
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markRead($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $notification = $user->unreadNotifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
