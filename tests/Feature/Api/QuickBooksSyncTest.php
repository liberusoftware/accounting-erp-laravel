<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\CreditMemo;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\QboConnection;
use App\Models\User;
use App\Services\QuickBooksService;
use App\Services\TeamManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QuickBooksSyncTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        app(TeamManagementService::class)->createPersonalTeamForUser($this->user);
        $this->user = $this->user->fresh();

        config()->set('services.qbo', [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'environment' => 'sandbox',
            'redirect_uri' => 'https://app.test/api/qbo/callback',
            'authorization_url' => 'https://appcenter.intuit.com/connect/oauth2',
            'token_url' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
            'api_base_url' => 'https://sandbox-quickbooks.api.intuit.com',
            'webhook_verifier_token' => 'test_verifier',
        ]);
    }

    public function test_connect_redirects_to_intuit_authorization_url(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/qbo/connect');

        $response->assertStatus(200)->assertJsonStructure(['authorization_url']);
        $this->assertStringContainsString('appcenter.intuit.com/connect/oauth2', $response->json('authorization_url'));
        $this->assertStringContainsString('client_id=test_client_id', $response->json('authorization_url'));
        $this->assertStringContainsString('state=', $response->json('authorization_url'));
    }

    public function test_callback_exchanges_code_and_stores_connection(): void
    {
        Http::fake([
            'oauth.platform.intuit.com/oauth2/v1/tokens/bearer' => Http::response([
                'access_token' => 'access-test-token',
                'refresh_token' => 'refresh-test-token',
                'expires_in' => 3600,
            ], 200),
        ]);

        Cache::put('qbo_oauth_state:'.$this->user->id, 'xyz', now()->addMinutes(10));

        $response = $this->actingAs($this->user)
            ->getJson('/api/qbo/callback?code=auth_code_123&realmId=4620816365&state=xyz');

        $response->assertStatus(200);
        $this->assertDatabaseHas('qbo_connections', [
            'user_id' => $this->user->id,
            'team_id' => $this->user->current_team_id,
            'realm_id' => '4620816365',
            'status' => 'active',
        ]);

        $connection = QboConnection::first();
        $this->assertSame('access-test-token', $connection->access_token);
        $this->assertSame('refresh-test-token', $connection->refresh_token);
    }

    public function test_callback_rejects_invalid_oauth_state(): void
    {
        Cache::put('qbo_oauth_state:'.$this->user->id, 'the-real-state', now()->addMinutes(10));

        $response = $this->actingAs($this->user)
            ->getJson('/api/qbo/callback?code=auth_code_123&realmId=4620816365&state=forged-state');

        $response->assertForbidden();
        $this->assertDatabaseCount('qbo_connections', 0);
    }

    public function test_push_invoice_creates_qbo_invoice_and_stores_remote_id(): void
    {
        Http::fake([
            '*/v3/company/*/invoice*' => Http::response([
                'Invoice' => ['Id' => '42', 'SyncToken' => '0'],
            ], 200),
        ]);

        $connection = $this->makeConnection();
        $customer = Customer::factory()->create(['team_id' => $this->user->current_team_id]);
        $invoice = Invoice::factory()->create(['team_id' => $this->user->current_team_id,
            'customer_id' => $customer->id,
            'total_amount' => 150.00,
        ]);

        app(QuickBooksService::class)->pushInvoice($invoice, $connection);

        $this->assertSame('42', $invoice->fresh()->qbo_id);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), '/v3/company/4620816365/invoice')
            && $request->method() === 'POST');
    }

    public function test_pull_invoices_creates_local_invoices(): void
    {
        Http::fake([
            '*/v3/company/*/query*' => Http::response([
                'QueryResponse' => [
                    'Invoice' => [
                        ['Id' => '99', 'DocNumber' => 'INV-099', 'TotalAmt' => 320.50, 'TxnDate' => '2026-06-01', 'CustomerRef' => ['value' => '7', 'name' => 'Globex']],
                    ],
                ],
            ], 200),
        ]);

        $connection = $this->makeConnection();

        $count = app(QuickBooksService::class)->pullInvoices($connection);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('invoices', [
            'qbo_id' => '99',
            'invoice_number' => 'INV-099',
        ]);
    }

    public function test_qbo_estimates_and_credit_memos_round_trip(): void
    {
        Http::fake([
            '*/v3/company/*/estimate' => Http::response(['Estimate' => ['Id' => 'estimate-1']], 200),
            '*/v3/company/*/creditmemo' => Http::response(['CreditMemo' => ['Id' => 'credit-1']], 200),
            '*/v3/company/*/query*' => Http::sequence()
                ->push(['QueryResponse' => ['Estimate' => [['Id' => 'estimate-9', 'DocNumber' => 'EST-9', 'TotalAmt' => 80, 'TxnDate' => '2026-09-01', 'CustomerRef' => ['value' => 'customer-9', 'name' => 'QBO Customer']]]]])
                ->push(['QueryResponse' => ['CreditMemo' => [['Id' => 'credit-9', 'DocNumber' => 'CM-9', 'TotalAmt' => 20, 'TxnDate' => '2026-09-02', 'CustomerRef' => ['value' => 'customer-9', 'name' => 'QBO Customer']]]]]),
        ]);

        $connection = $this->makeConnection();
        $customer = Customer::factory()->create(['team_id' => $this->user->current_team_id]);
        $estimate = Estimate::factory()->create(['team_id' => $this->user->current_team_id, 'customer_id' => $customer->id, 'total_amount' => 80]);
        $creditMemo = CreditMemo::create(['team_id' => $this->user->current_team_id, 'customer_id' => $customer->id, 'credit_memo_date' => '2026-09-02', 'total_amount' => 20, 'subtotal_amount' => 20]);

        app(QuickBooksService::class)->pushEstimate($estimate, $connection);
        app(QuickBooksService::class)->pushCreditMemo($creditMemo, $connection);

        $this->assertSame('estimate-1', $estimate->fresh()->qbo_id);
        $this->assertSame('credit-1', $creditMemo->fresh()->qbo_id);
        $this->assertSame(1, app(QuickBooksService::class)->pullEstimates($connection));
        $this->assertSame(1, app(QuickBooksService::class)->pullCreditMemos($connection));
        $this->assertDatabaseHas('estimates', ['qbo_id' => 'estimate-9', 'estimate_number' => 'EST-9']);
        $this->assertDatabaseHas('credit_memos', ['qbo_id' => 'credit-9', 'credit_memo_number' => 'CM-9']);
    }

    private function makeConnection(): QboConnection
    {
        return QboConnection::create([
            'user_id' => $this->user->id,
            'team_id' => $this->user->current_team_id,
            'realm_id' => '4620816365',
            'access_token' => 'access-test-token',
            'refresh_token' => 'refresh-test-token',
            'token_expires_at' => now()->addHour(),
            'status' => 'active',
        ]);
    }
}
