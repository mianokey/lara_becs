<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'default_days',
        'carry_forward',
        'max_carry_forward',
    ];

    protected $casts = [
        'carry_forward' => 'boolean',
    ];

    /**
     * A leave type can have many balances.
     */
    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /**
     * A leave type can have many requests.
     */
    public function requests()
    {
        return $this->hasMany(LeaveApplication::class);
    }
}
