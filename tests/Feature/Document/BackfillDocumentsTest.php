<?php

// src/tests/Feature/Document/BackfillDocumentsTest.php
declare(strict_types=1);

namespace Tests\Feature\Document;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\DocumentVersion;
use Liberu\Accounting\Erp\Models\Invoice;
use Tests\TestCase;

class BackfillDocumentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_backfill_registers_legacy_document_path_as_version_one(): void
    {
        $invoice = Invoice::factory()->create(['document_path' => 'legacy/invoice-42.pdf']);

        $this->artisan('documents:backfill')->assertSuccessful();

        $doc = $invoice->fresh()->documents()->first();
        $this->assertNotNull($doc);
        $this->assertSame('legacy/invoice-42.pdf', $doc->currentVersion->path);
        $this->assertSame(1, $doc->currentVersion->version_number);
    }

    public function test_backfill_is_idempotent(): void
    {
        $invoice = Invoice::factory()->create(['document_path' => 'legacy/invoice-42.pdf']);

        $this->artisan('documents:backfill');
        $this->artisan('documents:backfill');

        $this->assertSame(1, $invoice->fresh()->documents()->count());
    }

    public function test_backfill_registers_legacy_path_even_when_a_newer_document_exists(): void
    {
        $invoice = Invoice::factory()->create(['document_path' => 'legacy/invoice-42.pdf']);
        // A brand-new document attached (via the UI) before backfill ever runs.
        $newer = $invoice->documents()->create(['name' => 'new.pdf', 'disk' => 'local']);
        $newer->versions()->create(['version_number' => 1, 'path' => 'documents/new.pdf', 'original_filename' => 'new.pdf']);

        $this->artisan('documents:backfill')->assertSuccessful();

        // The legacy path must still be registered, not skipped because a doc already existed.
        $this->assertTrue(DocumentVersion::where('path', 'legacy/invoice-42.pdf')->exists());
    }
}
