<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CustomerPortal\Actions\CreateCustomerPortalRecord;
use Liberu\Accounting\CustomerPortal\Actions\PublishCustomerPortalRecord;
use Liberu\Accounting\CustomerPortal\Enums\CustomerPortalStatus;
use Liberu\Accounting\CustomerPortal\Exceptions\InvalidCustomerPortalRecord;

uses(RefreshDatabase::class);

it('tracks and publishes a customer invoice', function (): void {
    $record = app(CreateCustomerPortalRecord::class)->handle(['team_id' => 51, 'customer_id' => 'cust-1', 'type' => 'invoice', 'reference' => 'INV-1', 'currency' => 'GBP', 'amount' => 250, 'payload' => ['lines' => 2]]);
    $published = app(PublishCustomerPortalRecord::class)->handle($record);
    expect($published->status)->toBe(CustomerPortalStatus::Published)->and($published->published_at)->not->toBeNull();
});

it('rejects duplicate customer portal references', function (): void {
    $data = ['team_id' => 51, 'customer_id' => 'cust-1', 'type' => 'statement', 'reference' => 'ST-1'];
    app(CreateCustomerPortalRecord::class)->handle($data);
    expect(fn () => app(CreateCustomerPortalRecord::class)->handle($data))->toThrow(InvalidCustomerPortalRecord::class);
});
