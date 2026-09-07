<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\SalesInvoicing\Actions\ApproveInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\CreateInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\FinalizeInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\GenerateInvoicePdf;
use Liberu\Accounting\SalesInvoicing\Actions\SetInvoiceBranding;
use Liberu\Accounting\SalesInvoicing\Actions\SetRecurringSource;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;

uses(RefreshDatabase::class);
function invoicingParty(): Party
{
    $entity = DB::table('accounting_legal_entities')->insertGetId(['name' => 'Invoice Boundary Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now()]);

    return Party::create(['legal_entity_id' => $entity, 'type' => PartyType::Customer, 'name' => 'Invoice Boundary Customer']);
}
it('supports branding, recurring source, approval, finalization, and PDF boundary', function (): void {
    $invoice = app(CreateInvoice::class)->handle(['invoice_number' => 'INV-B-1', 'party_id' => invoicingParty()->id, 'invoice_date' => '2026-08-25', 'currency' => 'USD'], [['description' => 'Service', 'quantity' => 1, 'unit_price' => 100, 'tax_rate' => 10]]);
    app(SetInvoiceBranding::class)->handle($invoice, ['logo' => 'brand.svg']);
    app(SetRecurringSource::class)->handle($invoice->refresh(), ['source_type' => 'subscription', 'source_id' => 'sub-1']);
    app(ApproveInvoice::class)->handle($invoice->refresh());
    app(FinalizeInvoice::class)->handle($invoice->refresh());
    expect(app(GenerateInvoicePdf::class)->handle($invoice->refresh()))->toMatchArray(['format' => 'pdf', 'immutable' => true]);
});
it('does not permit draft PDF generation', function (): void {
    $invoice = app(CreateInvoice::class)->handle(['invoice_number' => 'INV-B-2', 'party_id' => invoicingParty()->id, 'invoice_date' => '2026-08-25', 'currency' => 'USD'], [['description' => 'Service', 'quantity' => 1, 'unit_price' => 10]]);
    expect(fn () => app(GenerateInvoicePdf::class)->handle($invoice))->toThrow(InvalidInvoice::class);
});
