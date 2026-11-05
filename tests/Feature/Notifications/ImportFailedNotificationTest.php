<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Liberu\Accounting\Erp\Models\BankStatement;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Notifications\ImportFailedNotification;
use Tests\TestCase;

class ImportFailedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_to_mail_and_database_channels(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $statement = BankStatement::factory()->create();

        $user->notify(new ImportFailedNotification($statement, 'boom'));

        Notification::assertSentTo(
            $user,
            ImportFailedNotification::class,
            fn ($notification, $channels): bool => in_array('mail', $channels, true) && in_array('database', $channels, true)
        );
    }

    public function test_array_payload_carries_error_and_statement_id(): void
    {
        $user = User::factory()->create();
        $statement = BankStatement::factory()->create();

        $payload = (new ImportFailedNotification($statement, 'boom'))->toArray($user);

        $this->assertSame('boom', $payload['error']);
        $this->assertSame($statement->getKey(), $payload['statement_id']);
    }

    public function test_mail_message_includes_the_error(): void
    {
        $user = User::factory()->create();
        $statement = BankStatement::factory()->create();

        $mail = (new ImportFailedNotification($statement, 'boom'))->toMail($user);

        $this->assertContains('Error: boom', $mail->introLines);
    }
}
