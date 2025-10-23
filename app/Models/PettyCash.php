<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_date',
        'request_type',
        'expense_type',
        'purpose',
        'amount',
        'currency',
        'project_id',
        'payee_name',
        'payment_mode',
        'account_details',
        'additional_notes',
        'receipt',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
