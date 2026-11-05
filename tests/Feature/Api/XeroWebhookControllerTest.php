<?php

use Illuminate\Support\Facades\Queue;
use Liberu\Accounting\Erp\Jobs\SyncAccountingProviderConnectionJob;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Models\XeroConnection;

it('rejects unsigned Xero webhooks', function () {
    config(['services.xero.webhook_key' => 'secret']);
    Queue::fake();

    $this->postJson('/api/webhooks/xero', ['events' => []])->assertUnauthorized();

    Queue::assertNothingPushed();
});

it('queues each active Xero tenant once for a signed payload', function () {
    config(['services.xero.webhook_key' => 'secret']);
    Queue::fake();
    $user = User::factory()->create();
    $connection = XeroConnection::create([
        'user_id' => $user->id,
        'tenant_id' => 'tenant-one',
        'access_token' => 'access',
        'refresh_token' => 'refresh',
        'token_expires_at' => now()->addHour(),
        'status' => 'active',
    ]);
    $payload = json_encode(['events' => [['tenantId' => 'tenant-one'], ['tenantId' => 'tenant-one']]], JSON_THROW_ON_ERROR);
    $signature = base64_encode(hash_hmac('sha256', $payload, 'secret', true));

    $this->call('POST', '/api/webhooks/xero', server: [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_XERO_SIGNATURE' => $signature,
    ], content: $payload)->assertSuccessful()->assertJsonPath('connections_queued', 1);

    Queue::assertPushed(SyncAccountingProviderConnectionJob::class, fn ($job) => $job->provider === 'xero' && $job->connectionId === $connection->id);
    Queue::assertPushed(SyncAccountingProviderConnectionJob::class, 1);
});
