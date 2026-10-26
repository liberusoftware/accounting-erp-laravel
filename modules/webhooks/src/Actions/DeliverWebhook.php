<?php

declare(strict_types=1);

namespace Liberu\Foundation\Webhooks\Actions;

use Illuminate\Support\Facades\Http;
use Liberu\Foundation\Webhooks\Models\WebhookDelivery;
use Liberu\Foundation\Webhooks\Support\RetrySchedule;
use Liberu\Foundation\Webhooks\Support\SigningSecretVault;
use Liberu\Foundation\Webhooks\Support\WebhookSigner;
use Liberu\Foundation\Webhooks\Support\WebhookUrlPolicy;

final readonly class DeliverWebhook
{
    public function __construct(private SigningSecretVault $vault, private WebhookSigner $signer, private RetrySchedule $schedule, private WebhookUrlPolicy $urls) {}

    public function handle(WebhookDelivery $delivery): WebhookDelivery
    {
        $endpoint = $delivery->endpoint;
        abort_unless($endpoint !== null, 404);
        $body = json_encode(['id' => $delivery->event_id, 'type' => $delivery->event, 'data' => $delivery->payload], JSON_THROW_ON_ERROR);
        $timestamp = time();
        $delivery->increment('attempts');
        try {
            $options = $this->urls->options($endpoint->url);
            $response = Http::withOptions($options)->timeout(10)->retry(2, 100, throw: false)->withHeaders(['Content-Type' => 'application/json', 'X-Webhook-Timestamp' => (string) $timestamp, 'X-Webhook-Signature' => $this->signer->sign($body, $this->vault->open($endpoint->signing_secret), $timestamp)])->withBody($body, 'application/json')->post($endpoint->url);
            $delivery->forceFill(['status' => $response->successful() ? 'delivered' : 'failed', 'response_status' => $response->status(), 'response_excerpt' => mb_substr($response->body(), 0, 1000), 'next_attempt_at' => $response->successful() ? null : now()->addSeconds($this->schedule->seconds((int) $delivery->attempts))])->save();
        } catch (\Throwable $exception) {
            $delivery->forceFill(['status' => 'failed', 'response_excerpt' => mb_substr($exception->getMessage(), 0, 1000), 'next_attempt_at' => now()->addSeconds($this->schedule->seconds((int) $delivery->attempts))])->save();
        }

        return $delivery->refresh();
    }
}
