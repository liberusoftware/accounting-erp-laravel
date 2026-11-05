<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Notifications\Concerns\ResolvesChannels;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ResolvesChannels;

    public function __construct(protected Invoice $invoice) {}

    public function via($notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toSms($notifiable): string
    {
        return 'Invoice #'.$this->invoice->id.' for $'
            .number_format($this->invoice->getTotalWithTax(), 2).' is due '
            .$this->invoice->invoice_date->format('Y-m-d').'.';
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Payment Reminder - Invoice #'.$this->invoice->id)
            ->greeting('Hello '.$this->invoice->customer->customer_name)
            ->line('This is a reminder that payment for Invoice #'.$this->invoice->id.' is overdue.')
            ->line('Amount due: $'.number_format($this->invoice->getTotalWithTax(), 2))
            ->line('Due date: '.$this->invoice->invoice_date->format('Y-m-d'))
            ->action('View Invoice', url('/invoices/'.$this->invoice->id))
            ->line('Thank you for your business!');
    }
}
