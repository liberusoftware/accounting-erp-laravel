<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\SupplierBills\Actions\AddSupplierBillCredit;
use Liberu\Accounting\SupplierBills\Actions\ApproveSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\AttachSupplierBillDocument;
use Liberu\Accounting\SupplierBills\Actions\CreateSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\MatchSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\PostSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\RejectSupplierBill;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Queries\DuplicateSupplierBillQuery;

uses(RefreshDatabase::class);

it('runs the supplier bill lifecycle and creates the payable open item on posting', function (): void {
    $party = supplierBillParty();
    $bill = app(CreateSupplierBill::class)->handle(['party_id' => $party->id, 'bill_number' => 'SUP-100', 'bill_date' => '2026-08-01', 'due_on' => '2026-08-31', 'currency' => 'USD', 'capture_source' => 'manual'], [['account_code' => '6100', 'description' => 'Materials', 'quantity' => 2, 'unit_price' => 100, 'tax_rate' => 10]]);

    expect((float) $bill->total)->toBe(220.0)->and($bill->status)->toBe(SupplierBillStatus::Draft);
    app(ApproveSupplierBill::class)->handle($bill, 1);
    $posted = app(PostSupplierBill::class)->handle($bill->fresh());

    expect($posted->status)->toBe(SupplierBillStatus::Posted)
        ->and(PayableOpenItem::query()->where('source_id', (string) $posted->id)->exists())->toBeTrue();
});

it('rejects duplicate supplier bill numbers and supports credits, matching, and documents', function (): void {
    $party = supplierBillParty();
    $bill = app(CreateSupplierBill::class)->handle(['party_id' => $party->id, 'bill_number' => 'SUP-101', 'bill_date' => '2026-08-01', 'currency' => 'USD'], [['description' => 'Service', 'quantity' => 1, 'unit_price' => 100]]);
    expect(fn () => app(CreateSupplierBill::class)->handle(['party_id' => $party->id, 'bill_number' => 'SUP-101', 'bill_date' => '2026-08-01', 'currency' => 'USD'], [['description' => 'Duplicate', 'quantity' => 1, 'unit_price' => 1]]))->toThrow(InvalidSupplierBill::class);
    app(ApproveSupplierBill::class)->handle($bill, 1);
    app(AddSupplierBillCredit::class)->handle($bill->fresh(), ['amount' => 20, 'reason' => 'Returned service']);
    app(MatchSupplierBill::class)->handle($bill->fresh(), ['match_type' => 'purchase_order', 'matched_type' => 'purchase_orders', 'matched_id' => 'po-1', 'amount' => 80]);
    app(AttachSupplierBillDocument::class)->handle($bill->fresh(), ['path' => 'supplier-bills/sup-101.pdf', 'original_name' => 'sup-101.pdf', 'mime_type' => 'application/pdf', 'sha256' => hash('sha256', 'sup-101')]);

    expect(app(DuplicateSupplierBillQuery::class)->handle($party->id, 'SUP-101'))->toHaveCount(1)
        ->and($bill->fresh()->credits)->toHaveCount(1)
        ->and($bill->fresh()->matches)->toHaveCount(1)
        ->and($bill->fresh()->documents)->toHaveCount(1);
});

it('rejects a draft with an explicit reason', function (): void {
    $bill = app(CreateSupplierBill::class)->handle(['party_id' => supplierBillParty()->id, 'bill_number' => 'SUP-102', 'bill_date' => '2026-08-01', 'currency' => 'USD'], [['description' => 'Service', 'quantity' => 1, 'unit_price' => 10]]);
    expect(app(RejectSupplierBill::class)->handle($bill, 'Missing supporting evidence')->status)->toBe(SupplierBillStatus::Rejected);
});

function supplierBillParty(): Party
{
    $legalEntityId = DB::table('accounting_legal_entities')->insertGetId(['name' => 'Supplier Bills Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now()]);

    return Party::create(['legal_entity_id' => $legalEntityId, 'type' => PartyType::Supplier, 'name' => 'Supplier Bills Vendor']);
}
