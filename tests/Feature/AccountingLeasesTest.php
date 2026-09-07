<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Leases\Actions\CreateDisclosure;
use Liberu\Accounting\Leases\Actions\CreateLease;
use Liberu\Accounting\Leases\Actions\GenerateSchedule;
use Liberu\Accounting\Leases\Actions\ModifyLease;
use Liberu\Accounting\Leases\Actions\PostPayment;
use Liberu\Accounting\Leases\Enums\LeaseStatus;
use Liberu\Accounting\Leases\Enums\PaymentStatus;
use Liberu\Accounting\Leases\Exceptions\InvalidLease;
use Liberu\Accounting\Leases\Queries\LeaseQuery;

uses(RefreshDatabase::class);

it('generates a lease schedule, posts payment, modifies, and discloses it', function (): void {
    $lease = app(CreateLease::class)->handle(['team_id' => 1, 'lease_ref' => 'LEASE-1', 'name' => 'Office', 'lessor_ref' => 'lessor-1', 'asset_ref' => 'property-1', 'commencement_date' => '2026-01-01', 'end_date' => '2028-01-01', 'currency' => 'GBP', 'payment_amount' => 1000, 'discount_rate' => .06, 'useful_life_months' => 24]);
    $lease = app(GenerateSchedule::class)->handle($lease);
    $payment = $lease->payments->first();
    $posted = app(PostPayment::class)->handle($payment);
    $lease = $lease->fresh();
    $activeStatus = $lease->status;
    $modified = app(ModifyLease::class)->handle($lease, ['modification_ref' => 'MOD-1', 'effective_date' => '2026-06-01', 'new_payment_amount' => 1100, 'reason' => 'Renewal']);
    $disclosure = app(CreateDisclosure::class)->handle($modified, '2026-06-30');
    expect($activeStatus)->toBe(LeaseStatus::Active)->and($payment->status)->toBe(PaymentStatus::Posted)->and($posted->status)->toBe(PaymentStatus::Posted)->and($modified->status)->toBe(LeaseStatus::Modified)->and($disclosure->remaining_liability)->toEqual((string) $modified->lease_liability)->and(app(LeaseQuery::class)->disclosure($modified)['scheduled_payments'])->toBeGreaterThan(0);
});

it('rejects invalid lease terms and premature payment posting', function (): void {
    expect(fn (): mixed => app(CreateLease::class)->handle(['lease_ref' => 'BAD', 'lessor_ref' => 'l', 'commencement_date' => '2026-03-01', 'end_date' => '2026-01-01', 'currency' => 'GBP', 'payment_amount' => 100, 'useful_life_months' => 12]))->toThrow(InvalidLease::class);
    $lease = app(CreateLease::class)->handle(['lease_ref' => 'LEASE-2', 'lessor_ref' => 'l', 'commencement_date' => '2026-01-01', 'end_date' => '2026-02-01', 'currency' => 'GBP', 'payment_amount' => 100, 'useful_life_months' => 12]);
    expect(fn (): mixed => app(PostPayment::class)->handle($lease->payments()->create(['payment_ref' => 'p', 'due_date' => '2026-02-01', 'amount' => 100])))->toThrow(InvalidLease::class);
});

it('exposes authenticated leases API routes', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.leases.read', 'accounting.leases.write']);
    $this->postJson('/api/v1/accounting/leases', ['team_id' => 1, 'lease_ref' => 'API-LEASE-1', 'name' => 'Vehicle', 'lessor_ref' => 'lessor', 'commencement_date' => '2026-01-01', 'end_date' => '2027-01-01', 'currency' => 'GBP', 'payment_amount' => 500, 'useful_life_months' => 12])->assertCreated()->assertJsonPath('data.type', 'accounting-lease');
    $this->getJson('/api/v1/accounting/leases')->assertOk();
});
