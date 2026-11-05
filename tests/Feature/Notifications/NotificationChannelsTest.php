<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Expense;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Models\UserNotificationPreference;
use Liberu\Accounting\Erp\Notifications\Channels\SmsChannel;
use Liberu\Accounting\Erp\Notifications\ExpenseApprovalNotification;
use Liberu\Accounting\Erp\Notifications\PaymentReminderNotification;
use Tests\TestCase;

class NotificationChannelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_notification_defaults_to_mail_and_database(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $user->notify(new ExpenseApprovalNotification(new Expense(['amount' => 100]), 'approved'));

        Notification::assertSentTo(
            $user,
            ExpenseApprovalNotification::class,
            fn ($n, array $channels): bool => in_array('mail', $channels, true) && in_array('database', $channels, true)
        );
    }

    public function test_database_channel_persists_a_row(): void
    {
        // Real send (not faked): proves the notifications table exists — the
        // channel was declared before but the table never did, so this errored.
        $user = User::factory()->create();
        UserNotificationPreference::create([
            'user_id' => $user->id,
            'mail_enabled' => false,
            'database_enabled' => true,
            'sms_enabled' => false,
        ]);

        $user->notifyNow(new ExpenseApprovalNotification(new Expense(['amount' => 50]), 'approved'));

        $this->assertSame(1, $user->notifications()->count());
    }

    public function test_preferences_gate_channels(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        // database off, mail on: the gate drops 'database' while the notification still sends via mail.
        UserNotificationPreference::create([
            'user_id' => $user->id,
            'mail_enabled' => true,
            'database_enabled' => false,
            'sms_enabled' => false,
        ]);

        $user->notify(new ExpenseApprovalNotification(new Expense(['amount' => 100]), 'approved'));

        Notification::assertSentTo(
            $user,
            ExpenseApprovalNotification::class,
            fn ($n, array $channels): bool => in_array('mail', $channels, true) && ! in_array('database', $channels, true)
        );
    }

    public function test_customer_sms_included_only_when_phone_present(): void
    {
        Notification::fake();
        $withPhone = Customer::factory()->create(['customer_phone' => '+15551234567']);
        $withoutPhone = Customer::factory()->create(['customer_phone' => '']);
        $invoice = Invoice::factory()->create();

        $withPhone->notify(new PaymentReminderNotification($invoice));
        $withoutPhone->notify(new PaymentReminderNotification($invoice));

        Notification::assertSentTo(
            $withPhone,
            PaymentReminderNotification::class,
            fn ($n, array $channels): bool => in_array(SmsChannel::class, $channels, true) && in_array('mail', $channels, true)
        );
        Notification::assertSentTo(
            $withoutPhone,
            PaymentReminderNotification::class,
            fn ($n, array $channels): bool => ! in_array(SmsChannel::class, $channels, true)
        );
    }

    public function test_to_sms_has_content(): void
    {
        $invoice = Invoice::factory()->create();
        $customer = Customer::factory()->create();

        $sms = (new PaymentReminderNotification($invoice))->toSms($customer);

        $this->assertNotEmpty($sms);
        $this->assertStringContainsString((string) $invoice->id, $sms);
    }
}
