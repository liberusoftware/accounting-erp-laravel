<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Liberu\Accounting\Erp\Models\Team;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

/**
 * Sends an SMS through the notifiable's TEAM Vonage account.
 *
 * Creds are resolved per team per send (not the app singleton), so each tenant
 * bills its own account. Any missing piece — no team, no creds, no phone —
 * short-circuits to a no-op: an unconfigured tenant never errors, and mail /
 * in-app channels are unaffected.
 */
class SmsChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $team = $this->resolveTeam($notifiable);
        if (! $team instanceof Team || ! $team->vonage_key || ! $team->vonage_secret || ! $team->vonage_from) {
            return;
        }

        $to = $notifiable->routeNotificationFor('sms', $notification);
        if (empty($to)) {
            return;
        }

        $client = new Client(new Basic((string) $team->vonage_key, (string) $team->vonage_secret));
        $client->sms()->send(
            new SMS((string) $to, (string) $team->vonage_from, (string) $notification->toSms($notifiable))
        );
    }

    private function resolveTeam(mixed $notifiable): ?Team
    {
        $teamId = $notifiable->team_id ?? $notifiable->current_team_id ?? null;

        return $teamId ? Team::find($teamId) : null;
    }
}
