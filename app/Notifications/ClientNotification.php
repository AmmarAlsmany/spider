<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ClientNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        // Conditionally add email channel if requested
        $channels = ['database'];

        if (isset($this->data['send_email']) && $this->data['send_email']) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->data['title'])
            ->line($this->data['message']);

        if (!empty($this->data['url'])) {
            $mail->action('View Details', $this->data['url']);
        }

        // Add high priority header if it's an emergency notification
        if (isset($this->data['is_emergency']) && $this->data['is_emergency']) {
            $mail->priority(1); // High priority
        }

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'url' => $this->data['url'] ?? null,
            'type' => $this->data['type'],
            'icon' => 'user-circle',
            'color' => 'primary',
            'priority' => $this->data['priority']
        ];
    }
}
