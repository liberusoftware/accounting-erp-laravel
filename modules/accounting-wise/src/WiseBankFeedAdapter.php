<?php

declare(strict_types=1);

namespace Liberu\Accounting\Wise;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Liberu\Accounting\BankFeeds\Contracts\BankFeedAdapter;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;
use Liberu\Foundation\Integrations\Contracts\IntegrationAdapter;
use Liberu\Foundation\Integrations\Contracts\PaymentProviderAdapter;

final class WiseBankFeedAdapter implements BankFeedAdapter, IntegrationAdapter, PaymentProviderAdapter
{
    public function __construct(private readonly ?BankFeedConnection $connection = null) {}

    public function name(): string
    {
        return 'wise';
    }

    public function capabilities(): array
    {
        return ['bank-feeds', 'profiles', 'balances', 'transfers', 'oauth'];
    }

    public function test(array $credentials): bool
    {
        return filled($credentials['client_id'] ?? null) && filled($credentials['client_secret'] ?? null);
    }

    public function authorizationUrl(string $state): string
    {
        return $this->authUrl().'?'.http_build_query(['client_id' => $this->clientId(), 'redirect_uri' => config('services.wise.redirect_uri'), 'response_type' => 'code', 'state' => $state, 'scope' => 'transfers balances.read']);
    }

    public function exchangeAuthorizationCode(string $code): array
    {
        return Http::timeout(15)->connectTimeout(5)->withBasicAuth($this->clientId(), (string) config('services.wise.client_secret', ''))->asForm()->post($this->baseUrl().'/oauth/v2/token', ['grant_type' => 'authorization_code', 'code' => $code, 'redirect_uri' => config('services.wise.redirect_uri')])->throw()->json();
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        return Http::timeout(15)->connectTimeout(5)->withBasicAuth($this->clientId(), (string) config('services.wise.client_secret', ''))->asForm()->post($this->baseUrl().'/oauth/v2/token', ['grant_type' => 'refresh_token', 'refresh_token' => $refreshToken])->throw()->json();
    }

    public function getProfiles(BankFeedConnection $connection): array
    {
        return Http::timeout(15)->connectTimeout(5)->withToken($connection->access_token)->get($this->baseUrl().'/v2/profiles')->throw()->json();
    }

    public function getBalances(BankFeedConnection $connection, int $profileId, string $type = 'STANDARD'): array
    {
        return Http::timeout(15)->connectTimeout(5)->withToken($connection->access_token)->get($this->baseUrl().'/v4/profiles/'.$profileId.'/balances', ['types' => $type])->throw()->json();
    }

    public function verifyWebhookSignature(string $bodyJson, string $signature, string $publicKey): bool
    {
        if ($publicKey === '' || $signature === '') {
            return false;
        }
        $decoded = base64_decode($signature, true);
        $key = openssl_pkey_get_public($publicKey);

        return $decoded !== false && $key !== false && openssl_verify($bodyJson, $decoded, $key, OPENSSL_ALGO_SHA256) === 1;
    }

    public function fetch(BankFeedConnection $connection): array
    {
        $credentials = $connection->credentials ?? [];
        $profile = $credentials['profile_id'] ?? null;
        if ($profile === null) {
            return ['added' => [], 'modified' => [], 'removed' => [], 'next_cursor' => $connection->cursor];
        }
        $response = Http::timeout(30)->connectTimeout(10)->retry(2, 200)->withToken($connection->access_token)->get($this->baseUrl().'/v1/transfers', ['profile' => $profile, 'limit' => 100, 'status' => $credentials['status'] ?? 'outgoing_payment_sent', 'createdDateStart' => $credentials['created_date_start'] ?? null, 'createdDateEnd' => $credentials['created_date_end'] ?? null]);
        $response->throw();
        $transfers = $response->json();
        $transfers = array_is_list($transfers) ? $transfers : ($transfers['transfers'] ?? []);

        return ['added' => array_map(fn (array $transfer): array => $this->normalize($transfer), $transfers), 'modified' => [], 'removed' => [], 'next_cursor' => $connection->cursor];
    }

    /** @param array<string, mixed> $payment */
    public function sendPayment(array $payment): array
    {
        return $this->paymentClient()->post($this->baseUrl().'/v1/transfers', $payment)->throw()->json();
    }

    /**
     * Wise has no provider-side bulk endpoint; each transfer is submitted through
     * the same authenticated, bounded client so orchestration can retry per item.
     *
     * @param  list<array<string, mixed>>  $payments
     * @return array{title: string, payments: list<array<string, mixed>>}
     */
    public function sendBulkPayments(string $title, array $payments, ?string $scheduleFor = null): array
    {
        if (count($payments) > 100) {
            throw new \InvalidArgumentException('Wise bulk submissions are limited to 100 transfers.');
        }

        return ['title' => $title, 'payments' => array_map(fn (array $payment): array => $this->sendPayment(array_merge($payment, ['schedule_for' => $scheduleFor])), $payments)];
    }

    private function normalize(array $transfer): array
    {
        $amount = $transfer['targetValue'] ?? $transfer['sourceValue'] ?? 0;

        return ['external_id' => (string) ($transfer['id'] ?? ''), 'transaction_date' => substr((string) ($transfer['createdDate'] ?? now()->toDateString()), 0, 10), 'amount' => -abs((float) $amount), 'currency' => $transfer['targetCurrency'] ?? $transfer['sourceCurrency'] ?? 'USD', 'status' => in_array($transfer['status'] ?? '', ['outgoing_payment_sent', 'completed'], true) ? 'posted' : 'pending', 'description' => $transfer['reference'] ?? 'Wise transfer', 'raw_data' => $transfer];
    }

    private function baseUrl(): string
    {
        return config('services.wise.environment', 'sandbox') === 'production' ? 'https://api.transferwise.com' : 'https://api.sandbox.transferwise.tech';
    }

    private function authUrl(): string
    {
        return config('services.wise.environment', 'sandbox') === 'production' ? 'https://wise.com/oauth/v2/authorize' : 'https://sandbox.transferwise.tech/oauth/v2/authorize';
    }

    private function clientId(): string
    {
        return (string) config('services.wise.client_id', '');
    }

    private function paymentClient(): PendingRequest
    {
        if ($this->connection === null) {
            throw new \RuntimeException('A connection is required for payments.');
        }

        return Http::timeout(30)->connectTimeout(10)->withToken($this->connection->access_token);
    }
}
