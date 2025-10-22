<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskMessage extends Model
{
    protected $fillable = ['task_id', 'user_id', 'message', 'read_at'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function task() {
        return $this->belongsTo(Task::class);
    }

    public function markAsRead() {
        $this->update(['read_at' => now()]);
    }

    public function isRead() {
        return !is_null($this->read_at);
    }
}
