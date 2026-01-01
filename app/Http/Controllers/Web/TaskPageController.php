<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskPageController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with('assignees')
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->priority, function ($q) use ($request) {
                $q->where('priority', $request->priority);
            })
            ->orderBy('due_date')
            ->get();

        $users = User::all();

        return view('tasks.index', compact('tasks', 'users'));
    }
}
