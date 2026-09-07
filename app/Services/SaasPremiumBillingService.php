<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PremiumCatalog;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class SaasPremiumBillingService
{
    private const STRIPE_API = 'https://api.stripe.com/v1';

    /** @return array<string, mixed> */
    public function createCheckoutSession(Team $team, string $interval, string $successUrl, string $cancelUrl): array
    {
        $this->ensureConfigured();
        $interval = $this->normaliseInterval($interval);
        $catalog = $this->ensureCatalog();
        $customerId = $this->ensureCustomer($team);

        return $this->request('post', '/checkout/sessions', [
            'mode' => 'subscription',
            'customer' => $customerId,
            'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'line_items' => [[
                'price' => $interval === 'year' ? $catalog->stripe_yearly_price_id : $catalog->stripe_monthly_price_id,
                'quantity' => 1,
            ]],
            'subscription_data' => [
                'trial_period_days' => (int) config('premium.trial_days', 14),
                'trial_settings' => ['end_behavior' => ['missing_payment_method' => 'cancel']],
                'metadata' => ['team_id' => (string) $team->getKey()],
            ],
            'payment_method_collection' => config('premium.card_required', true) ? 'always' : 'if_required',
            'metadata' => [
                'team_id' => (string) $team->getKey(),
                'billing_interval' => $interval,
            ],
        ]);
    }

    /** @return array<string, mixed> */
    public function createPortalSession(Team $team, string $returnUrl): array
    {
        $this->ensureConfigured();
        if (! $team->stripe_customer_id) {
            throw new RuntimeException('This team does not have a Stripe customer yet.');
        }

        return $this->request('post', '/billing_portal/sessions', [
            'customer' => $team->stripe_customer_id,
            'return_url' => $returnUrl,
        ]);
    }

    public function handleWebhook(string $payload, ?string $signature): void
    {
        $secret = (string) config('premium.webhook_secret');
        if ($secret === '' || ! $this->validSignature($payload, $signature, $secret)) {
            throw new RuntimeException('Invalid Stripe webhook signature.');
        }

        /** @var array<string, mixed> $event */
        $event = json_decode($payload, true, flags: JSON_THROW_ON_ERROR);
        $eventId = (string) ($event['id'] ?? '');
        $type = (string) ($event['type'] ?? '');
        $object = $event['data']['object'] ?? null;
        if ($eventId === '' || ! is_array($object)) {
            throw new RuntimeException('Malformed Stripe webhook payload.');
        }

        $team = $this->teamForStripeObject($object);
        if (! $team || $team->premium_last_event_id === $eventId) {
            return;
        }

        if (str_starts_with($type, 'customer.subscription.')) {
            $team->forceFill([
                'stripe_subscription_id' => (string) ($object['id'] ?? $team->stripe_subscription_id),
                'stripe_customer_id' => (string) ($object['customer'] ?? $team->stripe_customer_id),
                'stripe_price_id' => (string) ($object['items']['data'][0]['price']['id'] ?? $team->stripe_price_id),
                'premium_status' => (string) ($object['status'] ?? 'canceled'),
                'premium_trial_ends_at' => $this->timestamp($object['trial_end'] ?? null),
                'premium_current_period_ends_at' => $this->timestamp($object['current_period_end'] ?? null),
                'premium_cancel_at_period_end' => (bool) ($object['cancel_at_period_end'] ?? false),
                'premium_last_event_id' => $eventId,
            ])->save();
        } elseif ($type === 'checkout.session.completed') {
            $team->forceFill([
                'stripe_customer_id' => (string) ($object['customer'] ?? $team->stripe_customer_id),
                'stripe_subscription_id' => (string) ($object['subscription'] ?? $team->stripe_subscription_id),
                'stripe_product_id' => $team->stripe_product_id ?: PremiumCatalog::query()->value('stripe_product_id'),
                'premium_status' => 'trialing',
                'premium_trial_ends_at' => now()->addDays((int) config('premium.trial_days', 14)),
                'premium_last_event_id' => $eventId,
            ])->save();
        } elseif ($type === 'invoice.payment_failed') {
            $team->forceFill(['premium_status' => 'past_due', 'premium_last_event_id' => $eventId])->save();
        }
    }

    private function ensureConfigured(): void
    {
        if (! config('premium.enabled', false)) {
            throw new RuntimeException('SaaS Premium is disabled.');
        }
        if (! is_string(config('premium.secret')) || config('premium.secret') === '') {
            throw new RuntimeException('Stripe Premium billing is not configured.');
        }
    }

    private function ensureCatalog(): PremiumCatalog
    {
        $catalog = PremiumCatalog::query()->where('catalog_key', 'premium')->first();
        if ($catalog) {
            return $catalog;
        }

        $product = $this->request('post', '/products', [
            'name' => (string) config('premium.product_name', 'Accounting ERP Premium'),
            'description' => 'Premium accounting automation and collaboration for growing teams.',
            'metadata' => ['catalog_key' => 'premium'],
        ]);
        $productId = (string) ($product['id'] ?? '');
        if ($productId === '') {
            throw new RuntimeException('Stripe did not return a Premium product id.');
        }

        $monthly = $this->createPrice($productId, (int) config('premium.monthly_amount', 499), 'month');
        $yearly = $this->createPrice($productId, (int) config('premium.yearly_amount', 4999), 'year');

        return PremiumCatalog::query()->create([
            'catalog_key' => 'premium',
            'stripe_product_id' => $productId,
            'stripe_monthly_price_id' => $monthly,
            'stripe_yearly_price_id' => $yearly,
        ]);
    }

    private function createPrice(string $productId, int $amount, string $interval): string
    {
        $price = $this->request('post', '/prices', [
            'product' => $productId,
            'currency' => config('premium.currency', 'gbp'),
            'unit_amount' => $amount,
            'recurring' => ['interval' => $interval],
        ]);

        return (string) ($price['id'] ?? throw new RuntimeException('Stripe did not return a Premium price id.'));
    }

    private function ensureCustomer(Team $team): string
    {
        if (is_string($team->stripe_customer_id) && $team->stripe_customer_id !== '') {
            return $team->stripe_customer_id;
        }

        $customer = $this->request('post', '/customers', [
            'name' => $team->name,
            'metadata' => ['team_id' => (string) $team->getKey()],
        ]);
        $id = (string) ($customer['id'] ?? '');
        if ($id === '') {
            throw new RuntimeException('Stripe did not return a customer id.');
        }

        $team->forceFill([
            'stripe_customer_id' => $id,
            'stripe_product_id' => PremiumCatalog::query()->where('catalog_key', 'premium')->value('stripe_product_id'),
        ])->save();

        return $id;
    }

    /** @param array<string, mixed> $object */
    private function teamForStripeObject(array $object): ?Team
    {
        $teamId = $object['metadata']['team_id'] ?? null;
        if (is_string($teamId) && ctype_digit($teamId)) {
            $team = Team::query()->find((int) $teamId);
            if ($team) {
                return $team;
            }
        }

        $customerId = $object['customer'] ?? null;

        return is_string($customerId) && $customerId !== ''
            ? Team::query()->where('stripe_customer_id', $customerId)->first()
            : null;
    }

    private function normaliseInterval(string $interval): string
    {
        return match ($interval) {
            'monthly', 'month' => 'month',
            'yearly', 'year' => 'year',
            default => throw new RuntimeException('Premium billing interval must be monthly or yearly.'),
        };
    }

    private function validSignature(string $payload, ?string $signature, string $secret): bool
    {
        if (! is_string($signature) || $signature === '') {
            return false;
        }

        $timestamp = null;
        $signatures = [];
        foreach (explode(',', $signature) as $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, '');
            if (trim($key) === 't') {
                $timestamp = trim($value);
            } elseif (trim($key) === 'v1') {
                $signatures[] = trim($value);
            }
        }
        if (! is_string($timestamp) || ! ctype_digit($timestamp) || $signatures === [] || abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        return array_any($signatures, fn (string $provided): bool => hash_equals($expected, $provided));
    }

    private function timestamp(mixed $value): ?Carbon
    {
        return is_numeric($value) ? Carbon::createFromTimestamp((int) $value) : null;
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    private function request(string $method, string $path, array $payload): array
    {
        $response = Http::asForm()
            ->withBasicAuth((string) config('premium.secret'), '')
            ->acceptJson()
            ->{$method}(self::STRIPE_API.$path, $payload);

        if ($response->failed()) {
            throw new RuntimeException('Stripe request failed: '.(string) $response->json('error.message', 'Unknown Stripe error'));
        }

        /** @var array<string, mixed> $json */
        $json = $response->json();

        return $json;
    }
}
