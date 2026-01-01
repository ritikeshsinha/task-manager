<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $user1 = User::create([
            'name' => 'Ravi Kumar',
            'email' => 'ravi@test.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::create([
            'name' => 'Anjali Sharma',
            'email' => 'anjali@test.com',
            'password' => Hash::make('password'),
        ]);

        // Tasks
        $task1 = Task::create([
            'title' => 'Prepare Project Report',
            'description' => 'Final year project documentation',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDay(),
            'user_id' => $admin->id,
        ]);

        $task2 = Task::create([
            'title' => 'Fix Login Bug',
            'description' => 'Resolve Sanctum login issue',
            'status' => 'in_progress',
            'priority' => 'medium',
            'due_date' => now()->addDays(2),
            'user_id' => $admin->id,
        ]);

        $task3 = Task::create([
            'title' => 'UI Improvement',
            'description' => 'Improve dashboard UI',
            'status' => 'completed',
            'priority' => 'low',
            'due_date' => now()->subDay(),
            'user_id' => $admin->id,
        ]);

        // Assignments
        TaskAssignment::create([
            'task_id' => $task1->id,
            'assigned_to' => $user1->id,
            'assigned_by' => $admin->id,
        ]);

        TaskAssignment::create([
            'task_id' => $task2->id,
            'assigned_to' => $user2->id,
            'assigned_by' => $admin->id,
        ]);
    }
}
