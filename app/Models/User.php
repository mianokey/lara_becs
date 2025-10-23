<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles,SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // -------------------------------
    // Relationships
    // -------------------------------

    public function details()
    {
        return $this->hasMany(UserDetail::class);
    }
    

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    // -------------------------------
    // Helpers and Accessors
    // -------------------------------

    /**
     * Get a specific detail by key.
     */
    public function detail($key, $default = null)
    {
        return $this->details()->where('key', $key)->value('value') ?? $default;
    }

    /**
     * Get the user's phone number directly as $user->phone
     */
    public function getPhoneAttribute()
    {
        return $this->details()
            ->where('key', 'phone')
            ->value('value');
    }

    /**
     * Get all user details as an associative array.
     */
    public function getAllDetailsAttribute()
    {
        return $this->details()
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * (Optional) Allow dynamic access to custom detail keys.
     * Example: $user->department or $user->location
     */
    public function __get($key)
    {
        if ($this->details()->where('key', $key)->exists()) {
            return $this->details()->where('key', $key)->value('value');
        }

        return parent::__get($key);
    }
}
