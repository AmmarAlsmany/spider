<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TechnicalNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'url' => $this->data['url'] ?? null,
            'type' => $this->data['type'],
            'icon' => 'cog',
            'color' => 'warning',
            'priority' => $this->data['priority']
        ];
    }
}
