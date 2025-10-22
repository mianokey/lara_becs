<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'status',
        'consortium',
        'client_name',
        'description',
        'startDate',
        'endDate',
    ];
    

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
