<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Summary Counts
        $totalTasks      = Task::count();
        $pendingTasks    = Task::where('status', 'pending')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $completedTasks  = Task::where('status', 'completed')->count();
        $overdueTasks    = Task::where('due_date', '<', now())
                                ->where('status', '!=', 'completed')->count();

        // Latest 5 tasks
        $latestTasks = Task::with('assignees')
                           ->latest()
                           ->take(5)
                           ->get();

        // Notifications (unread)
        $notifications = $user->unreadNotifications()->take(5)->get();

        // Task Completion Rate
        $completionRate = $totalTasks > 0 
                          ? round(($completedTasks / $totalTasks) * 100, 2) 
                          : 0;

        // User Performance: tasks assigned vs completed per user
        $usersPerformance = User::withCount([
            'assignedTasks as totalAssigned',
            'assignedTasks as completedTasks' => function($q){
                $q->where('status','completed');
            }
        ])->get();

        // Chart Data
        $statusChart = [
            'pending'     => $pendingTasks,
            'in_progress' => $inProgressTasks,
            'completed'   => $completedTasks,
            'overdue'     => $overdueTasks,
        ];

        $priorityChart = [
            'low'    => Task::where('priority','low')->count(),
            'medium' => Task::where('priority','medium')->count(),
            'high'   => Task::where('priority','high')->count(),
        ];

        return view('dashboard', compact(
            'user', 'totalTasks','pendingTasks','inProgressTasks','completedTasks','overdueTasks',
            'latestTasks','notifications','completionRate','usersPerformance',
            'statusChart','priorityChart'
        ));
    }
}