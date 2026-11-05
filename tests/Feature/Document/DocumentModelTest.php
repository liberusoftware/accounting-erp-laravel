<?php

declare(strict_types=1);

namespace Tests\Feature\Document;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\DocumentVersion;
use Liberu\Accounting\Erp\Models\Invoice;
use Tests\TestCase;

class DocumentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_belongs_to_an_owner_and_tracks_current_version(): void
    {
        $invoice = Invoice::factory()->create();
        $doc = $invoice->documents()->create(['name' => 'contract.pdf', 'disk' => 'local']);
        DocumentVersion::create(['document_id' => $doc->id, 'version_number' => 1, 'path' => 'documents/a.pdf', 'original_filename' => 'contract.pdf', 'size' => 100]);
        DocumentVersion::create(['document_id' => $doc->id, 'version_number' => 2, 'path' => 'documents/b.pdf', 'original_filename' => 'contract.pdf', 'size' => 120]);

        $this->assertTrue($invoice->documents->contains($doc));
        $this->assertSame(2, $doc->fresh()->currentVersion->version_number);
    }
}
