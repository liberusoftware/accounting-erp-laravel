<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Mileage\Actions\ApproveTrip;
use Liberu\Accounting\Mileage\Actions\CaptureTrip;
use Liberu\Accounting\Mileage\Actions\CreateVehicle;
use Liberu\Accounting\Mileage\Actions\RecordMileageRate;
use Liberu\Accounting\Mileage\Actions\ReimburseTrip;
use Liberu\Accounting\Mileage\Actions\SubmitTrip;
use Liberu\Accounting\Mileage\Enums\TripStatus;
use Liberu\Accounting\Mileage\Exceptions\InvalidMileage;
use Liberu\Accounting\Mileage\Queries\MileageQuery;

uses(RefreshDatabase::class);

it('captures, submits, approves, reimburses, and reports a tenant trip', function (): void {
    $vehicle = app(CreateVehicle::class)->handle(['team_id' => 1, 'registration' => 'ab12 cde', 'owner_ref' => 'employee-1', 'make' => 'Ford']);
    $rate = app(RecordMileageRate::class)->handle(['team_id' => 1, 'region' => 'GB', 'vehicle_type' => 'car', 'currency' => 'GBP', 'rate_per_distance' => 0.45, 'effective_from' => '2026-01-01']);
    $trip = app(CaptureTrip::class)->handle(['team_id' => 1, 'trip_ref' => 'TRIP-1', 'employee_ref' => 'employee-1', 'vehicle_id' => $vehicle->id, 'rate_id' => $rate->id, 'trip_date' => '2026-01-10', 'distance' => 100, 'business_purpose' => 'Client visit', 'region' => 'GB', 'currency' => 'GBP', 'rate_per_distance' => 0.45]);
    expect($trip->reimbursement_amount)->toEqual('45.00')->and($trip->status)->toBe(TripStatus::Draft);
    $trip = app(SubmitTrip::class)->handle($trip);
    $trip = app(ApproveTrip::class)->handle($trip, 'manager-1');
    $reimbursement = app(ReimburseTrip::class)->handle($trip, 'employee-1', 'PAY-1');
    expect($reimbursement->amount)->toEqual('45.00')->and($trip->fresh()->status)->toBe(TripStatus::Reimbursed)->and(app(MileageQuery::class)->regionalReport(1, 'GB')['total_distance'])->toBe(100.0);
});

it('rejects invalid and duplicate trip inputs', function (): void {
    expect(fn (): mixed => app(CaptureTrip::class)->handle(['team_id' => 1, 'trip_ref' => 'BAD', 'employee_ref' => 'e', 'trip_date' => '2026-01-01', 'distance' => 0, 'region' => 'GB', 'currency' => 'GBP']))->toThrow(InvalidMileage::class);
    $data = ['team_id' => 1, 'trip_ref' => 'DUP-1', 'employee_ref' => 'e', 'trip_date' => '2026-01-01', 'distance' => 10, 'business_purpose' => 'Purpose', 'region' => 'GB', 'currency' => 'GBP', 'rate_per_distance' => .4];
    $first = app(CaptureTrip::class)->handle($data);
    $same = app(CaptureTrip::class)->handle($data);
    expect($same->id)->toBe($first->id);
    expect(fn (): mixed => app(CaptureTrip::class)->handle(array_merge($data, ['distance' => 11])))->toThrow(InvalidMileage::class);
});

it('exposes authenticated mileage API routes', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.mileage.read', 'accounting.mileage.write']);
    $this->postJson('/api/v1/accounting/mileage', ['team_id' => 1, 'trip_ref' => 'API-1', 'employee_ref' => 'employee-1', 'trip_date' => '2026-01-01', 'distance' => 12, 'business_purpose' => 'Delivery', 'region' => 'GB', 'currency' => 'GBP', 'rate_per_distance' => .45])->assertCreated()->assertJsonPath('data.type', 'accounting-mileage');
    $this->getJson('/api/v1/accounting/mileage')->assertOk();
    $this->getJson('/api/v1/accounting/mileage/report/regional?team_id=1')->assertOk();
});
