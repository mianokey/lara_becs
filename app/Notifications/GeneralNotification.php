<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;
    protected $receiverId;

    /**
     * Create a new notification instance.
     *
     * @param array $data  Example: [
     *   'title' => 'New Task Assigned',
     *   'message' => 'You have been assigned to Task #42',
     *   'url' => '/tasks/42',
     *   'icon' => 'fa-tasks',
     *   'sender_id' => 1,
     *   'sender_name' => 'Admin',
     * ]
     * @param int $receiverId
     */
    public function __construct(array $data, $receiverId)
    {
        $this->data = $data;
        $this->receiverId = $receiverId;
    }

    /**
     * Define notification delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Store notification in the database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->data['title'] ?? 'Notification',
            'message' => $this->data['message'] ?? '',
            'url' => $this->data['url'] ?? null,
            'icon' => $this->data['icon'] ?? 'fa-bell',
            'sender_id' => $this->data['sender_id'] ?? null,
            'sender_name' => $this->data['sender_name'] ?? null,
            'receiver_id' => $this->receiverId,
        ];
    }

    /**
     * Broadcast the notification for real-time updates.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->data['title'] ?? 'Notification',
            'message' => $this->data['message'] ?? '',
            'url' => $this->data['url'] ?? null,
            'icon' => $this->data['icon'] ?? 'fa-bell',
            'sender_id' => $this->data['sender_id'] ?? null,
            'sender_name' => $this->data['sender_name'] ?? null,
            'receiver_id' => $this->receiverId,
        ]);
    }

    /**
     * Broadcast on a private channel to the receiver.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->receiverId);
    }
}
