<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Erp\Models\BankConnection;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\TeamManagementService;
use Tests\TestCase;

class RevolutPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        app(TeamManagementService::class)->createPersonalTeamForUser($this->user);
        $this->user = $this->user->fresh();

        config()->set('services.revolut', [
            'client_id' => 'test_client',
            'client_secret' => 'test_secret',
            'environment' => 'sandbox',
            'redirect_uri' => 'https://app.test/api/revolut/callback',
        ]);
    }

    private function connection(?int $teamId = null): BankConnection
    {
        return BankConnection::create([
            'user_id' => $this->user->id,
            'team_id' => $teamId ?? $this->user->current_team_id,
            'bank_id' => 'revolut',
            'institution_name' => 'Revolut Business',
            'revolut_access_token' => 'access-token',
            'revolut_token_expires_at' => now()->addHour(),
            'status' => 'active',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'idempotency_key' => 'key-123',
            'account_id' => 'acc-1',
            'receiver' => ['counterparty_id' => 'cp-1'],
            'amount' => 100.00,
            'currency' => 'GBP',
            'reference' => 'Invoice 42',
        ], $overrides);
    }

    public function test_send_payment_forwards_idempotency_key_as_revolut_request_id(): void
    {
        Http::fake(['sandbox-b2b.revolut.com/api/1.0/pay' => Http::response(['id' => 'pay-1', 'state' => 'pending'], 200)]);
        $connection = $this->connection();
        Sanctum::actingAs($this->user, ['payments:write']);

        $response = $this->postJson("/api/revolut/connections/{$connection->id}/pay", $this->payload());

        $response->assertCreated()->assertJsonPath('payment.id', 'pay-1');
        Http::assertSent(fn ($req) => str_contains($req->url(), '/pay') && $req['request_id'] === 'key-123');
    }

    public function test_duplicate_idempotency_key_is_rejected(): void
    {
        Http::fake(['sandbox-b2b.revolut.com/api/1.0/pay' => Http::response(['id' => 'pay-1'], 200)]);
        $connection = $this->connection();
        Sanctum::actingAs($this->user, ['payments:write']);

        $this->postJson("/api/revolut/connections/{$connection->id}/pay", $this->payload())->assertCreated();

        // Same key again -> blocked before hitting Revolut a second time.
        $this->postJson("/api/revolut/connections/{$connection->id}/pay", $this->payload())->assertStatus(409);
        Http::assertSentCount(1);
    }

    public function test_send_payment_requires_idempotency_key(): void
    {
        $connection = $this->connection();
        Sanctum::actingAs($this->user, ['payments:write']);

        $this->postJson("/api/revolut/connections/{$connection->id}/pay", $this->payload(['idempotency_key' => null]))
            ->assertStatus(422);
    }

    public function test_send_payment_rejects_other_teams_connection(): void
    {
        Http::fake();
        $other = User::factory()->create();
        app(TeamManagementService::class)->createPersonalTeamForUser($other);
        $foreign = BankConnection::create([
            'user_id' => $other->id,
            'team_id' => $other->fresh()->current_team_id,
            'bank_id' => 'revolut',
            'institution_name' => 'Revolut Business',
            'revolut_access_token' => 'x',
            'status' => 'active',
        ]);
        Sanctum::actingAs($this->user, ['payments:write']);

        $this->postJson("/api/revolut/connections/{$foreign->id}/pay", $this->payload())->assertStatus(403);
        Http::assertNothingSent();
    }

    public function test_send_payment_requires_payments_write_ability(): void
    {
        Http::fake();
        $connection = $this->connection();
        Sanctum::actingAs($this->user, ['invoices:read']); // no payments:write

        $this->postJson("/api/revolut/connections/{$connection->id}/pay", $this->payload())->assertStatus(403);
        Http::assertNothingSent();
    }

    public function test_oauth_callback_rejects_invalid_state(): void
    {
        Cache::put('revolut_oauth_state:'.$this->user->id, 'real-state', now()->addMinutes(10));

        $response = $this->actingAs($this->user)
            ->postJson('/api/revolut/callback', ['code' => 'authcode', 'state' => 'forged']);

        $response->assertStatus(422);
        $this->assertDatabaseCount('bank_connections', 0);
    }
}
