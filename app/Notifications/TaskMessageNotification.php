<?php

namespace App\Notifications;

use App\Models\TaskMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;

class TaskMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $receiverId;

    public function __construct(TaskMessage $message, $receiverId)
    {
        $this->message = $message;
        $this->receiverId = $receiverId; 
    }

    /**
     * Notification delivery channels
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast']; // store + send realtime
    }

    /**
     * Store notification in DB
     */
    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->message->task_id,
            'message_id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->user->name,
            'receiver_id' => $this->receiverId,
        ];
    }

    /**
     * Send realtime broadcast
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'task_id' => $this->message->task_id,
            'message_id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->user->name,
            'receiver_id' => $this->receiverId,
        ]);
    }
    /**
     * Private channel (target the receiver)
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->receiverId);
    }
}

