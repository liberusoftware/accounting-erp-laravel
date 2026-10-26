<?php

declare(strict_types=1);

namespace Liberu\Foundation\Webhooks\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Liberu\Foundation\Webhooks\Models\WebhookDelivery;
use Liberu\Foundation\Webhooks\Models\WebhookEndpoint;

final class QueueWebhook
{
    /** @param array<string, mixed> $payload */
    public function handle(string $eventId, string $event, array $payload): int
    {
        $endpoints = WebhookEndpoint::query()->where('active', true)->get()->filter(static fn (WebhookEndpoint $endpoint): bool => in_array('*', $endpoint->events, true) || in_array($event, $endpoint->events, true));
        $queued = 0;
        DB::transaction(function () use ($endpoints, $eventId, $event, $payload, &$queued): void {
            foreach ($endpoints as $endpoint) {
                $delivery = WebhookDelivery::query()->firstOrCreate(['endpoint_id' => $endpoint->getKey(), 'event_id' => $eventId], ['id' => (string) Str::uuid(), 'event' => $event, 'payload' => $payload, 'status' => 'pending', 'attempts' => 0]);
                if ($delivery->wasRecentlyCreated) {
                    $queued++;
                }
            }
        });

        return $queued;
    }
}
