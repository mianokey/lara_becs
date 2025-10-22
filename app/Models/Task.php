<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $casts = [
        'is_weekly_deliverable' => 'boolean',
        'target_completion_date' => 'date',
    ];

    protected $fillable = [
        'title',
        'description',
        'assignee_id',
        'reviewer_id',
        'project_id',
        'priority',
        'status',
        'target_completion_date',
        'is_weekly_deliverable',
    ];


    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function messages()
    {
        return $this->hasMany(TaskMessage::class);
    }
}
