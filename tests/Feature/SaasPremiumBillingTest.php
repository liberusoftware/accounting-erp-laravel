<?php

declare(strict_types=1);

use App\Models\PremiumCatalog;
use App\Models\User;
use App\Services\TeamManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function premiumTeam(): array
{
    $user = User::factory()->create();
    $team = app(TeamManagementService::class)->createPersonalTeamForUser($user);
    $team->forceFill(['name' => 'Premium Team'])->save();

    return [$user->fresh(), $team->fresh()];
}

it('creates one Stripe product with monthly and yearly prices before checkout', function (): void {
    config()->set('premium.enabled', true);
    config()->set('premium.secret', 'sk_test_example');
    [$user, $team] = premiumTeam();

    Http::fake([
        'https://api.stripe.com/v1/products' => Http::response(['id' => 'prod_premium']),
        'https://api.stripe.com/v1/prices' => Http::sequence()
            ->push(['id' => 'price_monthly'])
            ->push(['id' => 'price_yearly']),
        'https://api.stripe.com/v1/customers' => Http::response(['id' => 'cus_team']),
        'https://api.stripe.com/v1/checkout/sessions' => Http::response(['url' => 'https://checkout.stripe.test/session']),
    ]);

    $response = $this->actingAs($user)->post(route('billing.premium.checkout'), ['interval' => 'year', '_token' => csrf_token()]);

    $response->assertRedirect('https://checkout.stripe.test/session');
    expect(PremiumCatalog::query()->sole()->toArray())->toMatchArray([
        'catalog_key' => 'premium',
        'stripe_product_id' => 'prod_premium',
        'stripe_monthly_price_id' => 'price_monthly',
        'stripe_yearly_price_id' => 'price_yearly',
    ]);
    expect($team->fresh()->stripe_customer_id)->toBe('cus_team');

    Http::assertSent(function (ClientRequest $request): bool {
        return $request->url() === 'https://api.stripe.com/v1/checkout/sessions'
            && $request['line_items'][0]['price'] === 'price_yearly'
            && $request['subscription_data']['trial_period_days'] === 14
            && $request['payment_method_collection'] === 'always';
    });
});

it('can start a cardless trial when explicitly configured', function (): void {
    config()->set('premium.enabled', true);
    config()->set('premium.secret', 'sk_test_example');
    config()->set('premium.card_required', false);
    [$user, $team] = premiumTeam();
    PremiumCatalog::query()->create([
        'catalog_key' => 'premium',
        'stripe_product_id' => 'prod_premium',
        'stripe_monthly_price_id' => 'price_monthly',
        'stripe_yearly_price_id' => 'price_yearly',
    ]);
    $team->forceFill(['stripe_customer_id' => 'cus_team'])->save();

    Http::fake(['https://api.stripe.com/v1/checkout/sessions' => Http::response(['url' => 'https://checkout.stripe.test/session'])]);

    $response = $this->actingAs($user)->post(route('billing.premium.checkout'), ['interval' => 'month', '_token' => csrf_token()]);

    $response->assertRedirect();
    Http::assertSent(fn (ClientRequest $request): bool => $request['payment_method_collection'] === 'if_required');
});

it('synchronizes Stripe subscription state per team and locks failed billing', function (): void {
    config()->set('premium.enabled', true);
    config()->set('premium.webhook_secret', 'whsec_example');
    [$user, $team] = premiumTeam();
    $team->forceFill(['stripe_customer_id' => 'cus_team'])->save();
    $event = [
        'id' => 'evt_subscription',
        'type' => 'customer.subscription.updated',
        'data' => ['object' => [
            'id' => 'sub_team',
            'customer' => 'cus_team',
            'status' => 'past_due',
            'trial_end' => null,
            'current_period_end' => now()->addMonth()->timestamp,
            'cancel_at_period_end' => false,
            'metadata' => ['team_id' => (string) $team->id],
            'items' => ['data' => [['price' => ['id' => 'price_monthly']]]],
        ]],
    ];
    $payload = json_encode($event, JSON_THROW_ON_ERROR);
    $timestamp = (string) time();
    $signature = $timestamp.'='.hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_example');

    $response = $this->postJson(route('stripe.webhook'), $event, ['Stripe-Signature' => 't='.$timestamp.',v1='.substr($signature, strlen($timestamp) + 1)]);

    $response->assertNoContent();
    expect($team->fresh()->premium_status)->toBe('past_due')
        ->and($team->fresh()->hasPremiumAccess())->toBeFalse();
});

it('does not expose the premium billing surface while Premium is disabled', function (): void {
    config()->set('premium.enabled', false);
    [$user] = premiumTeam();

    $this->actingAs($user)->get(route('billing.premium'))->assertNotFound();
});
