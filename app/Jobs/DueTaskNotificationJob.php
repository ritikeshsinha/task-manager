<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\DueTaskNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DueTaskNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // You can pass data here if needed
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Tasks due within next 24 hours
        $tasks = Task::whereBetween('due_date', [
                now(),
                now()->addDay()
            ])
            ->with('assignees')
            ->get();

        foreach ($tasks as $task) {
            foreach ($task->assignees as $user) {
                $user->notify(new DueTaskNotification($task));
            }
        }
    }
}
