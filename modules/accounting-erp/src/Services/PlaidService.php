<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Liberu\Accounting\Erp\Models\BankConnection;

class PlaidService
{
    protected string $clientId;

    protected string $secret;

    protected string $environment;

    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.plaid.client_id') ?? '';
        $this->secret = config('services.plaid.secret') ?? '';
        $this->environment = config('services.plaid.environment', 'sandbox') ?? 'sandbox';

        // Set base URL based on environment
        $this->baseUrl = match ($this->environment) {
            'production' => 'https://production.plaid.com',
            'development' => 'https://development.plaid.com',
            default => 'https://sandbox.plaid.com',
        };
    }

    /**
     * Handle Plaid API errors and determine if they're retryable
     */
    protected function handlePlaidError(array $errorData, string $context): void
    {
        $errorCode = $errorData['error_code'] ?? 'UNKNOWN_ERROR';
        $errorMessage = $errorData['error_message'] ?? 'Unknown error occurred';
        $errorType = $errorData['error_type'] ?? 'UNKNOWN';

        Log::error("Plaid API error in {$context}", [
            'error_code' => $errorCode,
            'error_type' => $errorType,
            'error_message' => $errorMessage,
        ]);

        // Determine if error requires user action
        $userActionRequired = in_array($errorCode, [
            'ITEM_LOGIN_REQUIRED',
            'INVALID_CREDENTIALS',
            'INVALID_MFA',
            'ITEM_LOCKED',
        ]);

        if ($userActionRequired) {
            throw new Exception("User action required: {$errorMessage} (Code: {$errorCode})");
        }

        throw new Exception("Plaid API error: {$errorMessage} (Code: {$errorCode})");
    }

    /**
     * Create a link token for Plaid Link initialization
     *
     * @param  int  $userId  User ID for Plaid identification
     * @param  string|null  $language  Language code (default: 'en')
     * @param  string|null  $accessToken  Existing access token for update mode (re-authentication)
     * @return array Link token data including token and expiration
     */
    public function createLinkToken(int $userId, ?string $language = 'en', ?string $accessToken = null): array
    {
        try {
            $payload = [
                'client_id' => $this->clientId,
                'secret' => $this->secret,
                'user' => [
                    'client_user_id' => (string) $userId,
                ],
                'client_name' => config('app.name'),
                'products' => ['transactions'],
                'country_codes' => ['US', 'CA', 'GB'],
                'language' => $language,
            ];

            // Add OAuth redirect URI if configured
            $oauthRedirectUri = config('services.plaid.oauth_redirect_uri');
            if ($oauthRedirectUri) {
                $payload['redirect_uri'] = $oauthRedirectUri;
            }

            // If access token is provided, this is update mode for re-authentication
            if ($accessToken) {
                $payload['access_token'] = $accessToken;
            }

            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->retry(3, 100)
                ->post("{$this->baseUrl}/link/token/create", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to create link token: '.$response->body());
        } catch (Exception $e) {
            Log::error('Plaid link token creation failed', [
                'user_id' => $userId,
                'update_mode' => $accessToken !== null,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Exchange a public token for an access token
     */
    public function exchangePublicToken(string $publicToken): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/item/public_token/exchange", [
                'client_id' => $this->clientId,
                'secret' => $this->secret,
                'public_token' => $publicToken,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to exchange public token: '.$response->body());
        } catch (Exception $e) {
            Log::error('Plaid public token exchange failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get institution details
     */
    public function getInstitution(string $institutionId): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/institutions/get_by_id", [
                'client_id' => $this->clientId,
                'secret' => $this->secret,
                'institution_id' => $institutionId,
                'country_codes' => ['US', 'CA', 'GB'],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get institution: '.$response->body());
        } catch (Exception $e) {
            Log::error('Plaid get institution failed', [
                'institution_id' => $institutionId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Sync transactions from Plaid
     */
    public function syncTransactions(BankConnection $connection): array
    {
        try {
            // Note: plaid_access_token is automatically decrypted by Laravel's encrypted cast
            $payload = [
                'client_id' => $this->clientId,
                'secret' => $this->secret,
                'access_token' => $connection->plaid_access_token,
            ];

            // Add cursor if we have it for incremental sync
            if ($connection->plaid_cursor) {
                $payload['cursor'] = $connection->plaid_cursor;
            }

            $response = Http::timeout(30)
                ->connectTimeout(10)
                ->retry(2, 200,
                    // Only retry on 5xx errors or network issues, not 4xx
                    fn ($exception, $request): bool => $exception instanceof ConnectionException ||
                       ($exception instanceof RequestException &&
                        $exception->response->status() >= 500))
                ->post("{$this->baseUrl}/transactions/sync", $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Update cursor for next sync
                if (isset($data['next_cursor'])) {
                    $connection->update([
                        'plaid_cursor' => $data['next_cursor'],
                        'last_synced_at' => now(),
                    ]);
                }

                return $data;
            }

            // Handle specific Plaid errors
            if ($response->status() === 400 && isset($response->json()['error_code'])) {
                $this->handlePlaidError($response->json(), 'syncTransactions');
            }

            throw new Exception('Failed to sync transactions: '.$response->body());
        } catch (Exception $e) {
            Log::error('Plaid transaction sync failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get account information
     *
     * @param  string  $accessToken  The access token (should already be decrypted if from model)
     */
    public function getAccounts(string $accessToken): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/accounts/get", [
                'client_id' => $this->clientId,
                'secret' => $this->secret,
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get accounts: '.$response->body());
        } catch (Exception $e) {
            Log::error('Plaid get accounts failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get account balances with real-time balance information
     *
     * @param  string  $accessToken  The access token (should already be decrypted if from model)
     * @param  array|null  $accountIds  Optional array of specific account IDs to get balances for
     */
    public function getBalances(string $accessToken, ?array $accountIds = null): array
    {
        try {
            $payload = [
                'client_id' => $this->clientId,
                'secret' => $this->secret,
                'access_token' => $accessToken,
            ];

            // Optionally filter to specific accounts
            if ($accountIds !== null && $accountIds !== []) {
                $payload['options'] = [
                    'account_ids' => $accountIds,
                ];
            }

            $response = Http::post("{$this->baseUrl}/accounts/balance/get", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get balances: '.$response->body());
        } catch (Exception $e) {
            Log::error('Plaid get balances failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Remove a Plaid item (disconnect bank)
     *
     * @param  string  $accessToken  The access token (should already be decrypted if from model)
     */
    public function removeItem(string $accessToken): bool
    {
        try {
            $response = Http::post("{$this->baseUrl}/item/remove", [
                'client_id' => $this->clientId,
                'secret' => $this->secret,
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return true;
            }

            throw new Exception('Failed to remove item: '.$response->body());
        } catch (Exception $e) {
            Log::error('Plaid item removal failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify webhook signature
     *
     * @param  string  $bodyJson  The raw JSON body of the webhook request
     * @param  array  $headers  The headers from the webhook request
     * @return bool True if signature is valid, false otherwise
     */
    public function verifyWebhookSignature(string $bodyJson, array $headers): bool
    {
        // Plaid signs webhooks with a per-message JWS (ES256) in the
        // Plaid-Verification header, NOT a shared HMAC secret. The JWT is signed
        // with a rotating key fetched from /webhook_verification_key/get by kid,
        // and its request_body_sha256 claim binds it to this exact body.
        $jwt = $this->extractVerificationHeader($headers);

        if ($jwt === null) {
            Log::warning('Plaid webhook: missing Plaid-Verification header');

            return false;
        }

        try {
            $segments = explode('.', $jwt);

            if (count($segments) !== 3) {
                return false;
            }

            $header = json_decode($this->base64UrlDecode($segments[0]), true);

            // Only ES256 is ever accepted — never 'none'/HS* (algorithm-confusion).
            if (! is_array($header) || ($header['alg'] ?? null) !== 'ES256' || empty($header['kid'])) {
                Log::warning('Plaid webhook: unexpected JWT header (alg/kid)');

                return false;
            }

            $jwk = $this->plaidVerificationKey((string) $header['kid']);

            if ($jwk === null) {
                return false;
            }

            // Verifies the ES256 signature against Plaid's public key. Throws on
            // any tamper/mismatch → caught below and rejected.
            JWT::$leeway = 60; // tolerate minor clock skew on iat
            $claims = JWT::decode($jwt, JWK::parseKey($jwk, 'ES256'));

            // Replay guard: Plaid JWTs carry iat; reject anything older than 5 min.
            $iat = (int) ($claims->iat ?? 0);

            if ($iat <= 0 || (now()->timestamp - $iat) > 300) {
                Log::warning('Plaid webhook: stale or missing iat');

                return false;
            }

            // Bind the token to THIS body: the JWT claims the sha256 of the raw body.
            $expected = $claims->request_body_sha256 ?? null;

            return is_string($expected) && hash_equals($expected, hash('sha256', $bodyJson));
        } catch (\Throwable $e) {
            Log::warning('Plaid webhook signature verification failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    private function extractVerificationHeader(array $headers): ?string
    {
        $value = $headers['plaid-verification'] ?? $headers['Plaid-Verification'] ?? null;

        // $request->headers->all() returns each header as an array of values.
        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * Fetch (and cache by kid) the JWK Plaid signs webhooks with. Only a
     * successful fetch is cached, so a transient outage doesn't poison it.
     * ponytail: 24h TTL per kid; a rotated key arrives under a new kid and
     * fetches fresh on its first webhook.
     *
     * @return array<string, mixed>|null
     */
    private function plaidVerificationKey(string $kid): ?array
    {
        $cacheKey = "plaid_jwk:{$kid}";
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $cached;
        }

        $response = Http::timeout(10)->post("{$this->baseUrl}/webhook_verification_key/get", [
            'client_id' => $this->clientId,
            'secret' => $this->secret,
            'key_id' => $kid,
        ]);

        $jwk = $response->successful() ? $response->json('key') : null;

        if (! is_array($jwk)) {
            Log::warning('Plaid webhook: could not fetch verification key', ['kid' => $kid]);

            return null;
        }

        Cache::put($cacheKey, $jwk, now()->addHours(24));

        return $jwk;
    }

    private function base64UrlDecode(string $data): string
    {
        return (string) base64_decode(strtr($data, '-_', '+/'), true);
    }
}
