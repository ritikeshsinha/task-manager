<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Events\TaskEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display all tasks with filters
     * GET /api/tasks
     */
    public function index(Request $request)
    {
        $tasks = Task::with('assignees')
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->priority, function ($q) use ($request) {
                $q->where('priority', $request->priority);
            })
            ->when($request->due_date, function ($q) use ($request) {
                $q->whereDate('due_date', $request->due_date);
            })
            ->latest()
            ->get();

        return response()->json($tasks);
    }

    /**
     * Store a new task
     * POST /api/tasks
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed',
            'priority'    => 'required|in:low,medium,high',
            'due_date'    => 'required|date',
        ]);

        $task = Task::create([
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status,
            'priority'    => $request->priority,
            'due_date'    => $request->due_date,
            'user_id'     => Auth::id(),
        ]);

        // Broadcast real-time event
        broadcast(new TaskEvent($task, 'created'))->toOthers();

        return response()->json([
            'message' => 'Task created successfully',
            'task'    => $task
        ], 201);
    }

    /**
     * Show single task details
     * GET /api/tasks/{id}
     */
    public function show($id)
    {
        $task = Task::with('assignees')->findOrFail($id);

        return response()->json($task);
    }

    /**
     * Update task details
     * PUT /api/tasks/{id}
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|in:pending,in_progress,completed',
            'priority'    => 'sometimes|in:low,medium,high',
            'due_date'    => 'sometimes|date',
        ]);

        $task->update($request->only([
            'title',
            'description',
            'status',
            'priority',
            'due_date'
        ]));

        broadcast(new TaskEvent($task, 'updated'))->toOthers();

        return response()->json([
            'message' => 'Task updated successfully',
            'task'    => $task
        ]);
    }

    /**
     * Soft delete task
     * DELETE /api/tasks/{id}
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        broadcast(new TaskEvent($task, 'deleted'))->toOthers();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
