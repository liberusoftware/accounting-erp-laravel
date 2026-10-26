<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Liberu\Foundation\Webhooks\Actions\DeliverWebhook;
use Liberu\Foundation\Webhooks\Actions\QueueWebhook;
use Liberu\Foundation\Webhooks\Actions\RegisterEndpoint;
use Liberu\Foundation\Webhooks\Actions\RotateEndpointSecret;
use Liberu\Foundation\Webhooks\Models\WebhookDelivery;
use Liberu\Foundation\Webhooks\Models\WebhookEndpoint;
use Liberu\Foundation\Webhooks\Support\RetrySchedule;
use Liberu\Foundation\Webhooks\Support\SigningSecretVault;

beforeEach(function (): void {
    $this->artisan('migrate');
});

it('registers, queues, delivers, and rotates webhook endpoints', function (): void {
    Http::fake(['https://8.8.8.8/*' => Http::response('accepted', 202)]);

    $endpoint = app(RegisterEndpoint::class)->handle('owner-1', 'https://8.8.8.8/hooks', ['post.created'], 'initial-secret');
    expect($endpoint)->toBeInstanceOf(WebhookEndpoint::class)
        ->and(app(SigningSecretVault::class)->open($endpoint->signing_secret))->toBe('initial-secret');

    expect(app(QueueWebhook::class)->handle('00000000-0000-0000-0000-000000000001', 'post.created', ['post_id' => '1']))->toBe(1)
        ->and(app(QueueWebhook::class)->handle('00000000-0000-0000-0000-000000000001', 'post.created', ['post_id' => '1']))->toBe(0);

    $delivery = WebhookDelivery::query()->firstOrFail();
    expect(app(DeliverWebhook::class)->handle($delivery)->status)->toBe('delivered')
        ->and($delivery->refresh()->attempts)->toBe(1)
        ->and($delivery->endpoint)->toBeInstanceOf(WebhookEndpoint::class);

    $newSecret = app(RotateEndpointSecret::class)->handle($endpoint->refresh());
    expect($newSecret)->toHaveLength(64)
        ->and(app(SigningSecretVault::class)->open($endpoint->refresh()->signing_secret))->toBe($newSecret);
});

it('uses bounded exponential retry delays', function (): void {
    $schedule = app(RetrySchedule::class);

    expect($schedule->seconds(0))->toBe(30)
        ->and($schedule->seconds(2))->toBe(60)
        ->and($schedule->seconds(20))->toBe(61440);
});

it('rejects invalid endpoint registration', function (): void {
    app(RegisterEndpoint::class)->handle('owner-1', 'ftp://example.test/hooks', ['post.created']);
})->throws(InvalidArgumentException::class);
