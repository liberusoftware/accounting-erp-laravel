<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Liberu\Accounting\Erp\Models\Expense;
use Liberu\Accounting\Erp\Notifications\Concerns\ResolvesChannels;

class ExpenseApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesChannels;

    public function __construct(
        protected Expense $expense,
        protected string $status
    ) {}

    public function via($notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toSms($notifiable): string
    {
        return "Your expense of {$this->expense->amount} has been {$this->status}.";
    }

    public function toMail($notifiable): MailMessage
    {
        $message = $this->status === 'approved'
            ? 'Your expense has been approved.'
            : 'Your expense has been rejected.';

        return (new MailMessage())
            ->subject("Expense {$this->status}")
            ->greeting("Hello {$notifiable->name}")
            ->line($message)
            ->line("Amount: {$this->expense->amount}")
            ->line("Description: {$this->expense->description}")
            ->when($this->status === 'rejected', fn ($mail) => $mail->line("Reason: {$this->expense->rejection_reason}"))
            ->line('Date: '.$this->expense->date->format('Y-m-d'));
    }

    public function toArray($notifiable): array
    {
        return [
            'expense_id' => $this->expense->id,
            'status' => $this->status,
            'amount' => $this->expense->amount,
            'reason' => $this->expense->rejection_reason,
            'approved_by' => $this->expense->approved_by,
            'approved_at' => $this->expense->approved_at,
        ];
    }
}
