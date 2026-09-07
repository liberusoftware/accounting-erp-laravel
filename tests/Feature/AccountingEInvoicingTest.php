<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\EInvoicing\Actions\ArchiveEInvoice;
use Liberu\Accounting\EInvoicing\Actions\CreateEInvoice;
use Liberu\Accounting\EInvoicing\Actions\ReconcileEInvoice;
use Liberu\Accounting\EInvoicing\Actions\RecordEInvoiceReceipt;
use Liberu\Accounting\EInvoicing\Actions\SignEInvoice;
use Liberu\Accounting\EInvoicing\Actions\SubmitEInvoice;
use Liberu\Accounting\EInvoicing\Actions\ValidateEInvoice;
use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Events\EInvoiceStatusChanged;
use Liberu\Accounting\EInvoicing\Exceptions\InvalidEInvoice;
use Tests\TestCase;

final class AccountingEInvoicingTest extends TestCase
{
    use RefreshDatabase;

    public function test_structured_document_lifecycle_records_receipts_and_archival(): void
    {
        Event::fake();
        $document = app(CreateEInvoice::class)->handle(['legal_entity_id' => 1, 'document_ref' => 'INV-001', 'document_type' => 'invoice', 'format' => 'ubl', 'tax_id' => 'GB123', 'counterparty_ref' => 'CUS-1', 'currency' => 'gbp', 'payload' => ['invoice_number' => 'INV-001', 'lines' => [['description' => 'Service', 'amount' => 100]]]]);
        $document = app(ValidateEInvoice::class)->handle($document, 'operator');
        $document = app(SignEInvoice::class)->handle($document, 'signature', 'operator');
        $document = app(SubmitEInvoice::class)->handle($document, 'peppol', 'operator');
        $document = app(RecordEInvoiceReceipt::class)->handle($document, true, null, 'provider');
        $document = app(ReconcileEInvoice::class)->handle($document, 'ledger');
        $document = app(ArchiveEInvoice::class)->handle($document, 'archiver');

        $this->assertSame(DocumentStatus::Archived, $document->status);
        $this->assertSame(['created', 'validated', 'signed', 'submitted', 'accepted', 'reconciled', 'archived'], $document->events()->orderBy('id')->pluck('event')->all());
        Event::assertDispatchedTimes(EInvoiceStatusChanged::class, 6);
    }

    public function test_documents_require_structured_lines_before_validation(): void
    {
        $document = app(CreateEInvoice::class)->handle(['legal_entity_id' => 1, 'document_ref' => 'INV-002', 'document_type' => 'credit', 'format' => 'ubl', 'tax_id' => 'GB123', 'counterparty_ref' => 'CUS-1', 'currency' => 'USD', 'payload' => ['invoice_number' => 'INV-002']]);
        $this->expectException(InvalidEInvoice::class);
        app(ValidateEInvoice::class)->handle($document);
    }

    public function test_api_write_scope_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['accounting.e-invoicing.read']);
        $this->postJson('/api/v1/accounting/e-invoicing', [])->assertForbidden();
    }
}
