<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Erp\Exceptions\ApprovalDeniedException;
use Liberu\Accounting\Erp\Models\ApprovalRule;
use Liberu\Accounting\Erp\Models\BankConnection;
use Liberu\Accounting\Erp\Models\OutboundPayment;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\ApprovalService;
use Liberu\Accounting\Erp\Services\TeamManagementService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OutboundPaymentApprovalTest extends TestCase
{
    use RefreshDatabase;

    private int $team;

    private User $maker;

    private BankConnection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        config()->set('services.revolut', [
            'client_id' => 'test_client',
            'client_secret' => 'test_secret',
            'environment' => 'sandbox',
            'redirect_uri' => 'https://app.test/api/revolut/callback',
        ]);

        $this->maker = User::factory()->create();
        app(TeamManagementService::class)->createPersonalTeamForUser($this->maker);
        $this->maker = $this->maker->fresh();
        $this->team = (int) $this->maker->current_team_id;
        Role::findOrCreate('approver', 'web');
        $this->maker->assignRole('approver');

        $this->connection = BankConnection::create([
            'user_id' => $this->maker->id,
            'team_id' => $this->team,
            'bank_id' => 'revolut',
            'institution_name' => 'Revolut Business',
            'revolut_access_token' => 'access-token',
            'revolut_token_expires_at' => now()->addHour(),
            'status' => 'active',
        ]);
    }

    private function payload(float $amount): array
    {
        return [
            'idempotency_key' => 'key-'.$amount,
            'account_id' => 'acc-1',
            'receiver' => ['counterparty_id' => 'cp-1'],
            'amount' => $amount,
            'currency' => 'GBP',
            'reference' => 'Invoice 42',
        ];
    }

    private function rule(float $threshold): void
    {
        ApprovalRule::create([
            'team_id' => $this->team,
            'approvable_type' => 'OutboundPayment',
            'min_amount' => $threshold,
            'steps' => ['approver'],
            'is_active' => true,
        ]);
    }

    public function test_payment_below_threshold_sends_immediately(): void
    {
        Http::fake(['sandbox-b2b.revolut.com/api/1.0/pay' => Http::response(['id' => 'pay-1'], 200)]);
        $this->rule(1000); // threshold 1000; a 500 payment is below it -> auto-approve

        Sanctum::actingAs($this->maker, ['payments:write']);
        $response = $this->postJson("/api/revolut/connections/{$this->connection->id}/pay", $this->payload(500));

        $response->assertCreated();
        Http::assertSent(fn ($r) => str_contains($r->url(), '/pay'));
        $this->assertSame('sent', OutboundPayment::first()->status);
    }

    public function test_payment_above_threshold_requires_approval(): void
    {
        Http::fake();
        $this->rule(1000);

        Sanctum::actingAs($this->maker, ['payments:write']);
        $response = $this->postJson("/api/revolut/connections/{$this->connection->id}/pay", $this->payload(5000));

        $response->assertStatus(202)->assertJsonPath('status', 'pending_approval');
        Http::assertNothingSent(); // no money moves until approved
        $this->assertDatabaseHas('outbound_payments', [
            'team_id' => $this->team,
            'status' => 'pending_approval',
            'approval_status' => 'pending',
        ]);
    }

    public function test_approval_by_a_second_user_executes_the_payment(): void
    {
        Http::fake(['sandbox-b2b.revolut.com/api/1.0/pay' => Http::response(['id' => 'pay-9'], 200)]);
        $this->rule(1000);

        Sanctum::actingAs($this->maker, ['payments:write']);
        $this->postJson("/api/revolut/connections/{$this->connection->id}/pay", $this->payload(5000))->assertStatus(202);

        // A different approver on the same team approves.
        $checker = User::factory()->create(['current_team_id' => $this->team]);
        $checker->assignRole('approver');

        $payment = OutboundPayment::first();
        $step = $payment->approvalRequest()->first()->steps()->first();
        app(ApprovalService::class)->approve($step, $checker);

        $payment->refresh();
        $this->assertSame('sent', $payment->status);
        Http::assertSent(fn ($r) => str_contains($r->url(), '/pay') && $r['request_id'] === $payment->idempotency_key);
    }

    public function test_maker_cannot_approve_their_own_payment(): void
    {
        Http::fake();
        $this->rule(1000);

        Sanctum::actingAs($this->maker, ['payments:write']);
        $this->postJson("/api/revolut/connections/{$this->connection->id}/pay", $this->payload(5000))->assertStatus(202);

        $payment = OutboundPayment::first();
        $step = $payment->approvalRequest()->first()->steps()->first();

        // Maker has the approver role, but maker != checker must block self-approval.
        $this->expectException(ApprovalDeniedException::class);
        app(ApprovalService::class)->approve($step, $this->maker->fresh());
    }
}
