<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Notifications\Concerns\ResolvesChannels;

class CollectionNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesChannels;

    public function __construct(protected Customer $customer) {}

    public function via($notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toSms($notifiable): string
    {
        $totalOverdue = $this->customer->invoices()
            ->where('payment_status', 'pending')
            ->sum('total_amount');

        return 'Your account is on credit hold: $'.number_format((float) $totalOverdue, 2)
            .' overdue. Please contact collections.';
    }

    public function toMail($notifiable): MailMessage
    {
        $totalOverdue = $this->customer->invoices()
            ->where('payment_status', 'pending')
            ->sum('total_amount');

        return (new MailMessage())
            ->subject('Important: Account Status Update')
            ->greeting('Dear '.$this->customer->customer_name)
            ->line('Your account has been placed on credit hold due to overdue payments.')
            ->line('Total amount overdue: $'.number_format($totalOverdue, 2))
            ->line('Please contact our collections department immediately to resolve this issue.')
            ->action('View Account', url('/account'))
            ->line('Thank you for your prompt attention to this matter.');
    }
}
