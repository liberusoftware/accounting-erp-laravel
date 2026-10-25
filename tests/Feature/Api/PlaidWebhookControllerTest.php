<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Jobs\SyncPlaidTransactionsJob;
use App\Models\BankConnection;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PlaidWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected BankConnection $connection;

    private string $privateKeyPem;

    /** @var array<string, mixed> */
    private array $jwk;

    private const KID = 'test-kid-1';

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.plaid.environment', 'sandbox');
        Config::set('services.plaid.client_id', 'test_client');
        Config::set('services.plaid.secret', 'test_secret');

        // Generate a throwaway EC P-256 keypair; expose the public half as the JWK
        // Plaid's key endpoint would return, and sign webhook JWTs with the private.
        $res = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']);
        openssl_pkey_export($res, $privatePem);
        $this->privateKeyPem = $privatePem;
        $details = openssl_pkey_get_details($res);

        $this->jwk = [
            'kty' => 'EC',
            'crv' => 'P-256',
            'x' => $this->b64url(str_pad($details['ec']['x'], 32, "\0", STR_PAD_LEFT)),
            'y' => $this->b64url(str_pad($details['ec']['y'], 32, "\0", STR_PAD_LEFT)),
            'kid' => self::KID,
            'use' => 'sig',
            'alg' => 'ES256',
        ];

        Cache::flush(); // JWK is cached by kid — isolate across the suite

        $this->user = User::factory()->create();
        $this->connection = BankConnection::factory()->create([
            'user_id' => $this->user->id,
            'plaid_item_id' => 'item_test_123',
            'status' => 'active',
        ]);
    }

    private function b64url(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    /** Plaid's key endpoint returns our test JWK. */
    private function fakeKeyOk(): void
    {
        Http::fake(['sandbox.plaid.com/webhook_verification_key/get' => Http::response(['key' => $this->jwk], 200)]);
    }

    /** Sign a Plaid-style webhook JWT binding to $bodyJson. */
    private function jwtFor(string $bodyJson, array $overrides = [], string $alg = 'ES256', ?string $key = null): string
    {
        $claims = array_merge([
            'iat' => time(),
            'request_body_sha256' => hash('sha256', $bodyJson),
        ], $overrides);

        return JWT::encode($claims, $key ?? $this->privateKeyPem, $alg, self::KID);
    }

    /** POST a payload with a correctly-signed verification JWT. */
    private function postSigned(array $payload)
    {
        $this->fakeKeyOk();
        $body = json_encode($payload);

        return $this->call('POST', '/api/webhooks/plaid', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_PLAID_VERIFICATION' => $this->jwtFor($body),
        ], $body);
    }

    public function test_rejects_a_non_jwt_signature(): void
    {
        $response = $this->postJson('/api/webhooks/plaid', ['webhook_type' => 'TRANSACTIONS'], [
            'Plaid-Verification' => 'not-a-jwt',
        ]);

        $response->assertStatus(401)->assertJson(['success' => false, 'message' => 'Invalid signature']);
    }

    public function test_accepts_a_valid_signature(): void
    {
        Queue::fake();

        $response = $this->postSigned([
            'webhook_type' => 'TRANSACTIONS',
            'webhook_code' => 'SYNC_UPDATES_AVAILABLE',
            'item_id' => $this->connection->plaid_item_id,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true, 'message' => 'Webhook processed']);
    }

    public function test_dispatches_sync_job_for_transactions_update(): void
    {
        Bus::fake();

        $this->postSigned([
            'webhook_type' => 'TRANSACTIONS',
            'webhook_code' => 'SYNC_UPDATES_AVAILABLE',
            'item_id' => $this->connection->plaid_item_id,
        ]);

        Bus::assertDispatched(SyncPlaidTransactionsJob::class, fn (SyncPlaidTransactionsJob $job): bool => $job->connectionId === $this->connection->id);
    }

    public function test_handles_item_error(): void
    {
        $this->postSigned([
            'webhook_type' => 'ITEM',
            'webhook_code' => 'ERROR',
            'item_id' => $this->connection->plaid_item_id,
            'error' => ['error_code' => 'ITEM_LOGIN_REQUIRED', 'error_message' => 'creds required'],
        ]);

        $this->assertDatabaseHas('bank_connections', ['id' => $this->connection->id, 'status' => 'requires_reauth']);
    }

    public function test_rejects_body_tampering(): void
    {
        // Sign a JWT for one body, then submit a DIFFERENT body: the
        // request_body_sha256 claim no longer matches -> reject.
        $this->fakeKeyOk();
        $signedBody = json_encode(['webhook_type' => 'TRANSACTIONS', 'item_id' => $this->connection->plaid_item_id]);
        $tamperedBody = json_encode(['webhook_type' => 'TRANSACTIONS', 'item_id' => 'attacker_item']);

        $response = $this->call('POST', '/api/webhooks/plaid', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_PLAID_VERIFICATION' => $this->jwtFor($signedBody),
        ], $tamperedBody);

        $response->assertStatus(401);
    }

    public function test_rejects_stale_iat(): void
    {
        $this->fakeKeyOk();
        $body = json_encode(['webhook_type' => 'TRANSACTIONS', 'item_id' => $this->connection->plaid_item_id]);

        $response = $this->call('POST', '/api/webhooks/plaid', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_PLAID_VERIFICATION' => $this->jwtFor($body, ['iat' => time() - 600]),
        ], $body);

        $response->assertStatus(401);
    }

    public function test_rejects_algorithm_confusion(): void
    {
        // An HS256 token must be rejected at the alg check, never verified.
        $body = json_encode(['webhook_type' => 'TRANSACTIONS', 'item_id' => $this->connection->plaid_item_id]);

        $response = $this->call('POST', '/api/webhooks/plaid', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_PLAID_VERIFICATION' => $this->jwtFor($body, [], 'HS256', str_repeat('x', 64)),
        ], $body);

        $response->assertStatus(401);
    }

    public function test_rejects_when_verification_key_unavailable(): void
    {
        // Plaid's key endpoint down -> cannot verify -> fail closed.
        Http::fake(['sandbox.plaid.com/webhook_verification_key/get' => Http::response([], 500)]);

        $body = json_encode(['webhook_type' => 'TRANSACTIONS', 'item_id' => $this->connection->plaid_item_id]);

        $response = $this->call('POST', '/api/webhooks/plaid', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_PLAID_VERIFICATION' => $this->jwtFor($body),
        ], $body);

        $response->assertStatus(401);
    }
}
