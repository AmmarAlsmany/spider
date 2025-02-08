<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FinancialNotification extends Notification
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
            'icon' => 'money-bill',
            'color' => 'success',
            'priority' => $this->data['priority']
        ];
    }
}
