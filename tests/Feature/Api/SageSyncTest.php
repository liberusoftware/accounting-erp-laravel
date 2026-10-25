<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SageConnection;
use App\Models\User;
use App\Models\Vendor;
use App\Services\SageService;
use App\Services\TeamManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SageSyncTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        app(TeamManagementService::class)->createPersonalTeamForUser($this->user);
        $this->user = $this->user->fresh();

        config()->set('services.sage', [
            'client_id' => 'test_client',
            'client_secret' => 'test_secret',
            'redirect_uri' => 'https://app.test/api/sage/callback',
            'authorization_url' => 'https://www.sageone.com/oauth2/auth/central',
            'token_url' => 'https://oauth.accounting.sage.com/token',
            'businesses_url' => 'https://api.accounting.sage.com/v3.1/businesses',
            'api_base_url' => 'https://api.accounting.sage.com/v3.1',
        ]);
    }

    private function connection(): SageConnection
    {
        return SageConnection::create([
            'user_id' => $this->user->id,
            'team_id' => $this->user->current_team_id,
            'business_id' => 'biz-123',
            'access_token' => 'at',
            'refresh_token' => 'rt',
            'token_expires_at' => now()->addHour(),
            'status' => 'active',
        ]);
    }

    private function service(): SageService
    {
        return app(SageService::class);
    }

    public function test_connect_returns_authorization_url(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/sage/connect');

        $response->assertOk()->assertJsonStructure(['authorization_url']);
        $this->assertStringContainsString('sageone.com/oauth2/auth/central', $response->json('authorization_url'));
        $this->assertStringContainsString('client_id=test_client', $response->json('authorization_url'));
    }

    public function test_callback_exchanges_code_and_stores_connection(): void
    {
        Http::fake([
            'oauth.accounting.sage.com/token' => Http::response(['access_token' => 'a', 'refresh_token' => 'r', 'expires_in' => 3600], 200),
            'api.accounting.sage.com/v3.1/businesses' => Http::response(['$items' => [['id' => 'biz-xyz']]], 200),
        ]);

        Cache::put('sage_oauth_state:'.$this->user->id, 'test-state', now()->addMinutes(10));

        $response = $this->actingAs($this->user)->getJson('/api/sage/callback?code=authcode&state=test-state');

        $response->assertOk();
        $this->assertDatabaseHas('sage_connections', ['user_id' => $this->user->id, 'team_id' => $this->user->current_team_id, 'business_id' => 'biz-xyz', 'status' => 'active']);
    }

    public function test_callback_rejects_invalid_oauth_state(): void
    {
        Cache::put('sage_oauth_state:'.$this->user->id, 'the-real-state', now()->addMinutes(10));

        $response = $this->actingAs($this->user)->getJson('/api/sage/callback?code=authcode&state=forged-state');

        $response->assertForbidden();
        $this->assertDatabaseCount('sage_connections', 0);
    }

    public function test_expired_token_is_refreshed_before_provider_request(): void
    {
        Http::fake([
            'oauth.accounting.sage.com/token' => Http::response(['access_token' => 'refreshed-at', 'refresh_token' => 'refreshed-rt', 'expires_in' => 3600]),
            '*/v3.1/sales_invoices*' => Http::response(['id' => 'sage-refreshed']),
        ]);

        $customer = Customer::factory()->create(['team_id' => $this->user->current_team_id]);
        $invoice = Invoice::factory()->create(['team_id' => $this->user->current_team_id, 'customer_id' => $customer->id]);
        $connection = $this->connection();
        $connection->update(['token_expires_at' => now()->subMinute()]);

        $this->service()->pushInvoice($invoice, $connection);

        Http::assertSent(fn ($request): bool => str_contains($request->url(), '/token') && $request->data()['grant_type'] === 'refresh_token');
        $this->assertSame('refreshed-at', $connection->fresh()->access_token);
        $this->assertSame('sage-refreshed', $invoice->fresh()->sage_id);
    }

    public function test_connections_can_be_listed_and_removed_for_current_team(): void
    {
        $connection = $this->connection();

        $this->actingAs($this->user)
            ->getJson('/api/sage/connections')
            ->assertOk()
            ->assertJsonPath('connections.0.business_id', $connection->business_id);

        $this->actingAs($this->user)
            ->deleteJson('/api/sage/connections/'.$connection->id)
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('sage_connections', ['id' => $connection->id]);
    }

    public function test_push_invoice_stores_remote_id(): void
    {
        Http::fake(['*/v3.1/sales_invoices*' => Http::response(['id' => 'sage-1'], 200)]);

        $customer = Customer::factory()->create(['team_id' => $this->user->current_team_id]);
        $invoice = Invoice::factory()->create(['team_id' => $this->user->current_team_id, 'customer_id' => $customer->id, 'total_amount' => 150]);

        $this->service()->pushInvoice($invoice, $this->connection());

        $this->assertSame('sage-1', $invoice->fresh()->sage_id);
        Http::assertSent(fn ($r) => str_contains($r->url(), '/v3.1/sales_invoices') && $r->method() === 'POST');
    }

    public function test_pull_invoices_creates_local_invoices(): void
    {
        Http::fake(['*/v3.1/sales_invoices*' => Http::response(['$items' => [
            ['id' => 'sage-9', 'displayed_as' => 'INV-S9', 'total_amount' => 320.50, 'date' => '2026-06-01', 'contact_name' => 'Globex'],
        ]], 200)]);

        $count = $this->service()->pullInvoices($this->connection());

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('invoices', ['sage_id' => 'sage-9']);
        $this->assertDatabaseHas('customers', ['customer_name' => 'Globex']);
    }

    public function test_push_and_pull_accounts_use_sage_ids(): void
    {
        Http::fake([
            '*/v3.1/ledger_accounts' => Http::sequence()
                ->push(['id' => 'sage-account-1'])
                ->push(['$items' => [['id' => 'sage-account-9', 'name' => 'Sales', 'nominal_code' => '4000', 'ledger_account_type' => ['name' => 'Income']]]]),
        ]);

        $account = Account::factory()->create(['team_id' => $this->user->current_team_id, 'account_name' => 'Cash', 'account_type' => 'asset', 'account_number' => 1000]);
        $connection = $this->connection();

        $this->service()->pushAccount($account, $connection);
        $count = $this->service()->pullAccounts($connection);

        $this->assertSame('sage-account-1', $account->fresh()->sage_id);
        $this->assertSame(1, $count);
        $this->assertDatabaseHas('accounts', ['sage_id' => 'sage-account-9', 'account_type' => 'revenue', 'account_number' => 4000]);
    }

    public function test_full_sync_reports_accounts_bills_and_payments(): void
    {
        $vendor = Vendor::factory()->create(['team_id' => $this->user->current_team_id, 'name' => 'Sage Vendor']);
        $invoice = Invoice::factory()->create(['team_id' => $this->user->current_team_id, 'customer_id' => Customer::factory()->state(['team_id' => $this->user->current_team_id]), 'sage_id' => 'sage-invoice-1']);
        Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => 40, 'payment_date' => '2026-09-02']);

        Http::fake([
            '*/v3.1/contacts*' => Http::response(['$items' => []]),
            '*/v3.1/ledger_accounts*' => Http::response(['$items' => []]),
            '*/v3.1/sales_invoices*' => Http::response(['$items' => []]),
            '*/v3.1/purchase_invoices*' => Http::response(['$items' => [['id' => 'sage-bill-1', 'reference' => 'PB-1', 'contact_name' => $vendor->name, 'total_amount' => 40, 'date' => '2026-09-01']]]),
            '*/v3.1/contact_payments*' => Http::response(['$items' => [['id' => 'sage-payment-1', 'invoice_id' => 'sage-invoice-1', 'total_amount' => 40, 'date' => '2026-09-02']]]),
            '*/v3.1/quotes*' => Http::response(['$items' => []]),
            '*/v3.1/sales_credit_notes*' => Http::response(['$items' => []]),
            '*/v3.1/bank_transactions*' => Http::response(['$items' => []]),
        ]);

        $counts = $this->service()->sync($this->connection());

        $this->assertSame(['customers' => 0, 'vendors' => 0, 'accounts' => 0, 'invoices' => 0, 'bills' => 1, 'payments' => 1, 'estimates' => 0, 'credit_memos' => 0, 'transactions' => 0], $counts);
        $this->assertDatabaseHas('bills', ['sage_id' => 'sage-bill-1', 'total_amount' => 40]);
        $this->assertDatabaseHas('payments', ['sage_id' => 'sage-payment-1', 'invoice_id' => $invoice->id]);
    }
}
