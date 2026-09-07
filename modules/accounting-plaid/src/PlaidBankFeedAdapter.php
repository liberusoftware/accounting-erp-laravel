<?php

declare(strict_types=1);

namespace Liberu\Accounting\Plaid;

use Illuminate\Support\Facades\Http;
use Liberu\Accounting\BankFeeds\Contracts\BankFeedAdapter;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;
use Liberu\Foundation\Integrations\Contracts\IntegrationAdapter;

final class PlaidBankFeedAdapter implements BankFeedAdapter, IntegrationAdapter
{
    public function name(): string
    {
        return 'plaid';
    }

    public function capabilities(): array
    {
        return ['bank-feeds', 'institutions', 'balances', 'incremental-sync', 'webhooks'];
    }

    public function test(array $credentials): bool
    {
        return filled($credentials['client_id'] ?? null) && filled($credentials['secret'] ?? null);
    }

    public function createLinkToken(int $userId, ?string $language = 'en', ?string $accessToken = null): array
    {
        $payload = ['client_id' => $this->clientId(), 'secret' => $this->secret(), 'user' => ['client_user_id' => (string) $userId], 'client_name' => config('app.name'), 'products' => ['transactions'], 'country_codes' => ['US', 'CA', 'GB'], 'language' => $language];
        if (config('services.plaid.oauth_redirect_uri')) {
            $payload['redirect_uri'] = config('services.plaid.oauth_redirect_uri');
        }
        if ($accessToken !== null) {
            $payload['access_token'] = $accessToken;
        }

        return Http::timeout(15)->connectTimeout(5)->retry(3, 100)->post($this->baseUrl().'/link/token/create', $payload)->throw()->json();
    }

    public function exchangePublicToken(string $publicToken): array
    {
        return Http::timeout(15)->connectTimeout(5)->post($this->baseUrl().'/item/public_token/exchange', ['client_id' => $this->clientId(), 'secret' => $this->secret(), 'public_token' => $publicToken])->throw()->json();
    }

    public function getInstitution(string $institutionId): array
    {
        return Http::timeout(15)->connectTimeout(5)->post($this->baseUrl().'/institutions/get_by_id', ['client_id' => $this->clientId(), 'secret' => $this->secret(), 'institution_id' => $institutionId, 'country_codes' => ['US', 'CA', 'GB']])->throw()->json();
    }

    public function getAccounts(string $accessToken): array
    {
        return Http::timeout(15)->connectTimeout(5)->post($this->baseUrl().'/accounts/get', ['client_id' => $this->clientId(), 'secret' => $this->secret(), 'access_token' => $accessToken])->throw()->json();
    }

    public function getBalances(string $accessToken, ?array $accountIds = null): array
    {
        $payload = ['client_id' => $this->clientId(), 'secret' => $this->secret(), 'access_token' => $accessToken];
        if ($accountIds !== null && $accountIds !== []) {
            $payload['options'] = ['account_ids' => $accountIds];
        }

        return Http::timeout(15)->connectTimeout(5)->post($this->baseUrl().'/accounts/balance/get', $payload)->throw()->json();
    }

    public function removeItem(string $accessToken): bool
    {
        Http::timeout(15)->connectTimeout(5)->post($this->baseUrl().'/item/remove', ['client_id' => $this->clientId(), 'secret' => $this->secret(), 'access_token' => $accessToken])->throw();

        return true;
    }

    public function fetch(BankFeedConnection $connection): array
    {
        $response = Http::timeout(30)->connectTimeout(10)->retry(2, 200)->post($this->baseUrl().'/transactions/sync', ['client_id' => $this->clientId(), 'secret' => $this->secret(), 'access_token' => $connection->access_token, 'cursor' => $connection->cursor]);
        $response->throw();
        $payload = $response->json();

        return ['added' => array_map(fn (array $transaction): array => $this->normalize($transaction), $payload['added'] ?? []), 'modified' => array_map(fn (array $transaction): array => $this->normalize($transaction), $payload['modified'] ?? []), 'removed' => array_map(fn (array $transaction): string => (string) $transaction['transaction_id'], $payload['removed'] ?? []), 'next_cursor' => $payload['next_cursor'] ?? null];
    }

    /** @return array<string, mixed> */
    private function normalize(array $transaction): array
    {
        $amount = (float) ($transaction['amount'] ?? 0);

        return ['external_id' => (string) ($transaction['transaction_id'] ?? ''), 'transaction_date' => $transaction['date'] ?? $transaction['authorized_date'] ?? now()->toDateString(), 'amount' => $amount > 0 ? -abs($amount) : abs($amount), 'currency' => $transaction['iso_currency_code'] ?? 'USD', 'status' => ($transaction['pending'] ?? false) ? 'pending' : 'posted', 'description' => $transaction['name'] ?? 'Plaid transaction', 'category' => isset($transaction['category']) ? strtolower((string) end($transaction['category'])) : 'uncategorized', 'raw_data' => $transaction];
    }

    private function baseUrl(): string
    {
        return match (config('services.plaid.environment', 'sandbox')) {
            'production' => 'https://production.plaid.com', 'development' => 'https://development.plaid.com', default => 'https://sandbox.plaid.com'
        };
    }

    private function clientId(): string
    {
        return (string) config('services.plaid.client_id', '');
    }

    private function secret(): string
    {
        return (string) config('services.plaid.secret', '');
    }
}
