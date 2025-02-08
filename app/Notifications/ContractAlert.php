<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $contract;
    protected $type;
    protected $report;

    public function __construct($contract = null, $type, $report = null)
    {
        $this->contract = $contract;
        $this->type = $type;
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)->subject('Contract Alert');

        switch ($this->type) {
            case 'expired':
                $message->line("Contract #{$this->contract->id} has expired!")
                    ->line("End Date: {$this->contract->end_date}")
                    ->action('View Contract', url("/contracts/{$this->contract->id}"));
                break;

            case 'payment_due':
                $message->line("Payment due for Contract #{$this->contract->id}")
                    ->line("Due Date: {$this->contract->payment_date}")
                    ->line("Amount: {$this->contract->amount}")
                    ->action('View Contract', url("/contracts/{$this->contract->id}"));
                break;

            case 'renewal_needed':
                $message->line("Contract #{$this->contract->id} needs renewal")
                    ->line("Expiry Date: {$this->contract->end_date}")
                    ->action('View Contract', url("/contracts/{$this->contract->id}"));
                break;

            case 'monthly_report':
                $message->line('Monthly Contract Report')
                    ->line("Generated at: {$this->report['generated_at']}")
                    ->line("Expired Contracts: {$this->report['expired_contracts']}")
                    ->line("Due Payments: {$this->report['due_payments']}")
                    ->line("Contracts Needing Renewal: {$this->report['contracts_needing_renewal']}")
                    ->action('View All Contracts', url('/contracts'));
                break;
        }

        return $message;
    }

    public function toArray($notifiable)
    {
        $data = [
            'type' => $this->type,
        ];

        if ($this->contract) {
            $data['contract_id'] = $this->contract->id;
        }

        if ($this->report) {
            $data['report'] = $this->report;
        }

        return $data;
    }
}
