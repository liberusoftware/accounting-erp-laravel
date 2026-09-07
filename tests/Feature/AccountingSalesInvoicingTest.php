<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\SalesInvoicing\Actions\ApproveInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\CreateInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\FinalizeInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\MarkInvoiceDelivered;
use Liberu\Accounting\SalesInvoicing\Actions\RecordDeposit;
use Liberu\Accounting\SalesInvoicing\Enums\InvoiceStatus;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Tests\TestCase;

class AccountingSalesInvoicingTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_lifecycle_calculates_totals_and_preserves_final_records(): void
    {
        $legalEntityId = DB::table('accounting_legal_entities')->insertGetId([
            'name' => 'Sales Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $party = Party::create(['legal_entity_id' => $legalEntityId, 'type' => PartyType::Customer, 'name' => 'Acme Customer']);

        $invoice = app(CreateInvoice::class)->handle([
            'invoice_number' => 'INV-1001', 'party_id' => $party->id, 'invoice_date' => '2026-08-24',
            'currency' => 'USD', 'branding' => ['logo' => 'brand-a'], 'recurring_source' => ['source_id' => 'sub-1'],
        ], [[
            'description' => 'Consulting', 'quantity' => 2, 'unit_price' => 100, 'discount_rate' => 10, 'tax_rate' => 20,
        ]]);

        $this->assertSame(InvoiceStatus::Draft, $invoice->status);
        $this->assertSame('200.00', (string) $invoice->subtotal);
        $this->assertSame('20.00', (string) $invoice->discount_total);
        $this->assertSame('36.00', (string) $invoice->tax_total);
        $this->assertSame('216.00', (string) $invoice->total);
        $this->assertSame('brand-a', $invoice->branding['logo']);

        $invoice = app(ApproveInvoice::class)->handle($invoice);
        $invoice = app(FinalizeInvoice::class)->handle($invoice, 'tester');
        $invoice = app(RecordDeposit::class)->handle($invoice, ['amount' => 100, 'reference' => 'PAY-1']);
        $invoice = app(MarkInvoiceDelivered::class)->handle($invoice);

        $this->assertSame(InvoiceStatus::Final, $invoice->status);
        $this->assertSame(116.0, $invoice->outstanding());
        $this->assertSame('delivered', $invoice->delivery_status);
        $this->assertDatabaseHas('accounting_sales_invoice_deposits', ['invoice_id' => $invoice->id, 'amount' => 100]);

        $this->expectException(\LogicException::class);
        $invoice->update(['notes' => 'must not change']);
    }

    public function test_invalid_invoice_lines_and_deposits_are_rejected(): void
    {
        $this->expectException(InvalidInvoice::class);
        app(CreateInvoice::class)->handle(['invoice_number' => 'INV-INVALID', 'currency' => 'USD'], [[
            'description' => 'Invalid', 'quantity' => 0, 'unit_price' => 100,
        ]]);
    }
}
