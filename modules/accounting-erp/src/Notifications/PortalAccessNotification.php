<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Emails a signed, expiring set-password link to a customer or vendor. Used for
 * both the initial invite and "forgot password" — the link is the only way in,
 * so it is time-limited and tamper-proof (Laravel signed URL).
 */
class PortalAccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $guard) {}

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            "portal.{$this->guard}.set-password",
            now()->addHours(24),
            // hash of the current password → the link stops working the moment a
            // password is set (single-use), so a leaked link can't take over later.
            ['id' => $notifiable->getKey(), 'hash' => sha1((string) $notifiable->password)],
        );

        return (new MailMessage())
            ->subject('Set up your portal access')
            ->line('Set a password to access your portal.')
            ->action('Set password', $url)
            ->line('This link expires in 24 hours. If you did not expect this, ignore it.');
    }
}
