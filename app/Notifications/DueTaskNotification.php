<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DueTaskNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Task Due Soon')
            ->line('Task "' . $this->task->title . '" is due soon.')
            ->action('View Tasks', url('/tasks'));
    }

    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'title'   => $this->task->title,
            'due'     => $this->task->due_date,
        ];
    }
}