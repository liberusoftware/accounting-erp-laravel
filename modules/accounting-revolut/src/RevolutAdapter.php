<?php

declare(strict_types=1);

namespace Liberu\Accounting\Revolut;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Liberu\Accounting\BankFeeds\Contracts\BankFeedAdapter;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;
use Liberu\Foundation\Integrations\Contracts\IntegrationAdapter;
use Liberu\Foundation\Integrations\Contracts\PaymentProviderAdapter;

final class RevolutAdapter implements BankFeedAdapter, IntegrationAdapter, PaymentProviderAdapter
{
    public function __construct(private readonly ?BankFeedConnection $connection = null) {}

    public function name(): string
    {
        return 'revolut';
    }

    public function capabilities(): array
    {
        return ['bank-feeds', 'accounts', 'balances', 'transactions', 'payments', 'bulk-payments'];
    }

    public function test(array $credentials): bool
    {
        return filled($credentials['client_id'] ?? null) && filled($credentials['client_secret'] ?? null);
    }

    public function authorizationUrl(string $state): string
    {
        return $this->authUrl().'?'.http_build_query(['client_id' => $this->clientId(), 'redirect_uri' => config('services.revolut.redirect_uri'), 'response_type' => 'code', 'state' => $state]);
    }

    public function exchangeAuthorizationCode(string $code): array
    {
        return Http::timeout(15)->connectTimeout(5)->post($this->baseUrl().'/auth/token', ['grant_type' => 'authorization_code', 'code' => $code, 'client_id' => $this->clientId(), 'client_secret' => $this->clientSecret(), 'redirect_uri' => config('services.revolut.redirect_uri')])->throw()->json();
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        return Http::timeout(15)->connectTimeout(5)->post($this->baseUrl().'/auth/token', ['grant_type' => 'refresh_token', 'refresh_token' => $refreshToken, 'client_id' => $this->clientId(), 'client_secret' => $this->clientSecret()])->throw()->json();
    }

    public function getAccounts(BankFeedConnection $connection): array
    {
        return $this->clientFor($connection)->get($this->baseUrl().'/accounts')->throw()->json();
    }

    public function getAccount(BankFeedConnection $connection, string $accountId): array
    {
        return $this->clientFor($connection)->get($this->baseUrl().'/accounts/'.$accountId)->throw()->json();
    }

    public function verifyWebhookSignature(string $bodyJson, string $signature): bool
    {
        $secret = (string) config('services.revolut.webhook_secret', '');

        return $secret !== '' && $signature !== '' && hash_equals('v1='.hash_hmac('sha256', $bodyJson, $secret), $signature);
    }

    public function fetch(BankFeedConnection $connection): array
    {
        $response = Http::timeout(30)->connectTimeout(10)->retry(2, 200)->withToken($connection->access_token)->get($this->baseUrl().'/transactions', ['count' => 100]);
        $response->throw();
        $transactions = $response->json();
        $transactions = array_is_list($transactions) ? $transactions : ($transactions['transactions'] ?? []);

        return ['added' => array_map(fn (array $transaction): array => $this->normalize($transaction), $transactions), 'modified' => [], 'removed' => [], 'next_cursor' => $connection->cursor];
    }

    public function sendPayment(array $payment): array
    {
        return $this->client()->post($this->baseUrl().'/pay', $payment)->throw()->json();
    }

    public function sendBulkPayments(string $title, array $payments, ?string $scheduleFor = null): array
    {
        return $this->client()->post($this->baseUrl().'/payment-drafts', array_filter(['title' => $title, 'payments' => $payments, 'schedule_for' => $scheduleFor]))->throw()->json();
    }

    private function normalize(array $transaction): array
    {
        $amount = (float) ($transaction['legs'][0]['amount'] ?? $transaction['amount'] ?? 0);
        $date = $transaction['completed_at'] ?? $transaction['created_at'] ?? now()->toDateString();

        return ['external_id' => (string) ($transaction['id'] ?? ''), 'transaction_date' => substr((string) $date, 0, 10), 'amount' => $amount < 0 ? -abs($amount) : abs($amount), 'currency' => $transaction['legs'][0]['currency'] ?? $transaction['currency'] ?? 'USD', 'status' => ($transaction['state'] ?? 'completed') === 'pending' ? 'pending' : 'posted', 'description' => $transaction['reference'] ?? $transaction['merchant']['name'] ?? 'Revolut transaction', 'category' => strtolower((string) ($transaction['type'] ?? 'uncategorized')), 'raw_data' => $transaction];
    }

    private function client(): PendingRequest
    {
        if ($this->connection === null) {
            throw new \RuntimeException('A connection is required for payments.');
        }

        return Http::timeout(30)->connectTimeout(10)->withToken($this->connection->access_token);
    }

    private function clientFor(BankFeedConnection $connection): PendingRequest
    {
        return Http::timeout(30)->connectTimeout(10)->withToken($connection->access_token);
    }

    private function clientId(): string
    {
        return (string) config('services.revolut.client_id', '');
    }

    private function clientSecret(): string
    {
        return (string) config('services.revolut.client_secret', '');
    }

    private function authUrl(): string
    {
        return config('services.revolut.environment', 'sandbox') === 'production' ? 'https://business.revolut.com/app-confirm' : 'https://sandbox-business.revolut.com/app-confirm';
    }

    private function baseUrl(): string
    {
        return config('services.revolut.environment', 'sandbox') === 'production' ? 'https://b2b.revolut.com/api/1.0' : 'https://sandbox-b2b.revolut.com/api/1.0';
    }
}
