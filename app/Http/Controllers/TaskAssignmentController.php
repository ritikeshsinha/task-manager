<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskAssignmentController extends Controller
{
    /**
     * Assign a task to multiple users
     * POST /api/tasks/{id}/assign
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'users'   => 'required|array',
            'users.*' => 'exists:users,id'
        ]);

        foreach ($request->users as $userId) {

            // Prevent duplicate assignment
            TaskAssignment::firstOrCreate([
                'task_id'     => $id,
                'assigned_to' => $userId
            ], [
                'assigned_by' => Auth::id()
            ]);
        }

        return response()->json([
            'message' => 'Task assigned successfully'
        ]);
    }

    /**
     * Get all assignees of a task
     * GET /api/tasks/{id}/assignees
     */
    public function assignees($id)
    {
        $task = Task::with('assignees')->findOrFail($id);

        return response()->json($task);
    }

    /**
     * Remove user from task
     * DELETE /api/tasks/{id}/assignees/{user_id}
     */
    public function remove($id, $userId)
    {
        TaskAssignment::where('task_id', $id)
            ->where('assigned_to', $userId)
            ->delete();

        return response()->json([
            'message' => 'User removed from task'
        ]);
    }
}
