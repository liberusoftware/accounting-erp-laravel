<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\SalesTaxAndGst\Actions\ActivateSalesTaxRecord;
use Liberu\Accounting\SalesTaxAndGst\Actions\CloseSalesTaxRecord;
use Liberu\Accounting\SalesTaxAndGst\Actions\CreateSalesTaxRecord;
use Liberu\Accounting\SalesTaxAndGst\Enums\SalesTaxRecordType;
use Liberu\Accounting\SalesTaxAndGst\Enums\SalesTaxStatus;
use Liberu\Accounting\SalesTaxAndGst\Exceptions\InvalidSalesTax;
use Liberu\Accounting\SalesTaxAndGst\Queries\SalesTaxRecordQuery;

uses(RefreshDatabase::class);
it('calculates and progresses a GST liability record', function (): void {
    $record = app(CreateSalesTaxRecord::class)->handle(['context_id' => 'entity-1', 'type' => SalesTaxRecordType::Liability, 'jurisdiction' => 'CA-ON', 'rate' => 13, 'taxable_base' => 1000, 'period_start' => '2026-01-01', 'period_end' => '2026-03-31']);
    expect((float) $record->liability)->toBe(130.0);
    app(ActivateSalesTaxRecord::class)->handle($record);
    expect($record->refresh()->status)->toBe(SalesTaxStatus::Active);
    expect(app(CloseSalesTaxRecord::class)->handle($record->refresh())->status)->toBe(SalesTaxStatus::Closed);
});
it('rejects invalid rates and queries return periods', function (): void {
    $data = ['context_id' => 'entity-2', 'type' => 'registration', 'jurisdiction' => 'US-CA', 'rate' => 101, 'period_start' => '2026-01-01', 'period_end' => '2026-03-31'];
    expect(fn () => app(CreateSalesTaxRecord::class)->handle($data))->toThrow(InvalidSalesTax::class);
    $record = app(CreateSalesTaxRecord::class)->handle(array_merge($data, ['rate' => 8.25]));
    expect(app(SalesTaxRecordQuery::class)->paginate('entity-2', 'registration', 'draft')->total())->toBe(1)->and($record->type)->toBe(SalesTaxRecordType::Registration);
});
