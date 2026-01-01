<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'user_id'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Task creator (who created the task)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Users assigned to this task
     */
    public function assignees()
    {
        return $this->belongsToMany(
            User::class,
            'task_assignments',
            'task_id',
            'assigned_to'
        )->withTimestamps();
    }
}
