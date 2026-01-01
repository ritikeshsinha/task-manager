<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $table = 'task_assignments';

    protected $fillable = [
        'task_id',
        'assigned_to',
        'assigned_by'
    ];

    /**
     * Assigned task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * User to whom task is assigned
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * User who assigned the task
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
