<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\ReminderSetting;
use Liberu\Accounting\Erp\Notifications\PaymentReminderNotification;

class SendPaymentReminders extends Command
{
    #[\Override]
    protected $signature = 'invoices:send-reminders';

    #[\Override]
    protected $description = 'Send payment reminders for overdue invoices';

    public function handle(): void
    {
        $settings = ReminderSetting::first();

        if (! $settings || ! $settings->is_active) {
            $this->info('Reminder system is not active');

            return;
        }

        $overdueInvoices = Invoice::where('payment_status', 'pending')
            ->where('invoice_date', '<=', Carbon::now()->subDays($settings->days_before_reminder))
            ->where(function ($query) use ($settings): void {
                $query->where('reminders_sent', '<', $settings->max_reminders)
                    ->orWhereNull('last_reminder_sent_at');
            })
            ->get();

        foreach ($overdueInvoices as $invoice) {
            if ($invoice->customer && $invoice->customer->email && (! $invoice->last_reminder_sent_at || Carbon::parse($invoice->last_reminder_sent_at)->addDays($settings->reminder_frequency_days)->isPast())) {
                $invoice->customer->notify(new PaymentReminderNotification($invoice));
                $invoice->reminders_sent++;
                $invoice->last_reminder_sent_at = now();
                $invoice->save();
                $this->info("Reminder sent for Invoice #{$invoice->id}");
            }
        }
    }
}
