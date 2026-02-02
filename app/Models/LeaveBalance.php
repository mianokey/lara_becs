<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'total_days',
        'used_days',
        'carried_forward',
        'balance_days',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Helper: Calculate the current balance dynamically.
     */
    public function getCalculatedBalanceAttribute(): int
    {
        return ($this->total_days + $this->carried_forward) - $this->used_days;
    }

    /**
     * Sync the balance_days column automatically when saving.
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->balance_days = ($model->total_days + $model->carried_forward) - $model->used_days;
        });
    }
}
