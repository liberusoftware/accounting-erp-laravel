<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Notifications\Concerns;

use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Notifications\Channels\SmsChannel;

/**
 * Chooses delivery channels per notifiable.
 *
 * Staff (User) honor their own UserNotificationPreference toggles (defaults:
 * mail on, in-app on, sms off). Everyone else (Customers) get mail, plus SMS
 * when they expose a phone via routeNotificationForSms(). Customers get no
 * 'database' channel — they have no panel to read a bell.
 */
trait ResolvesChannels
{
    /**
     * @return array<int, string>
     */
    protected function channelsFor(mixed $notifiable): array
    {
        if ($notifiable instanceof User) {
            $pref = $notifiable->notificationPreference;
            $channels = [];

            if ($pref->mail_enabled ?? true) {
                $channels[] = 'mail';
            }
            if ($pref->database_enabled ?? true) {
                $channels[] = 'database';
            }
            if (($pref->sms_enabled ?? false) && ! empty($pref->phone)) {
                $channels[] = SmsChannel::class;
            }

            return $channels;
        }

        $channels = ['mail'];
        if (! empty($notifiable->routeNotificationFor('sms'))) {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }
}
