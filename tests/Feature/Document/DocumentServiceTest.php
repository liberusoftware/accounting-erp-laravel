<?php

// src/tests/Feature/Document/DocumentServiceTest.php
declare(strict_types=1);

namespace Tests\Feature\Document;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Liberu\Accounting\Erp\Models\DocumentVersion;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Services\DocumentService;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('documents.disk'));
    }

    public function test_attach_stores_file_and_creates_version_one(): void
    {
        $invoice = Invoice::factory()->create();
        $doc = app(DocumentService::class)->attach($invoice, UploadedFile::fake()->create('c.pdf', 10));

        $this->assertSame(1, $doc->currentVersion->version_number);
        Storage::disk(config('documents.disk'))->assertExists($doc->currentVersion->path);
    }

    public function test_add_version_increments(): void
    {
        $invoice = Invoice::factory()->create();
        $service = app(DocumentService::class);
        $doc = $service->attach($invoice, UploadedFile::fake()->create('c.pdf', 10));
        $service->addVersion($doc, UploadedFile::fake()->create('c2.pdf', 12));

        $this->assertSame(2, $doc->fresh()->currentVersion->version_number);
        $this->assertCount(2, $doc->fresh()->versions);
    }

    public function test_prune_deletes_expired_documents_and_files(): void
    {
        $invoice = Invoice::factory()->create();
        $service = app(DocumentService::class);
        $doc = $service->attach($invoice, UploadedFile::fake()->create('c.pdf', 10), now()->subDay());
        $path = $doc->currentVersion->path;

        $deleted = $service->prune();

        $this->assertSame(1, $deleted);
        $this->assertDatabaseMissing('documents', ['id' => $doc->id]);
        Storage::disk(config('documents.disk'))->assertMissing($path);
    }

    public function test_prune_keeps_documents_without_retention(): void
    {
        $invoice = Invoice::factory()->create();
        app(DocumentService::class)->attach($invoice, UploadedFile::fake()->create('c.pdf', 10));

        $this->assertSame(0, app(DocumentService::class)->prune());
    }

    public function test_duplicate_version_number_is_rejected_by_the_unique_index(): void
    {
        $invoice = Invoice::factory()->create();
        $doc = app(DocumentService::class)->attach($invoice, UploadedFile::fake()->create('c.pdf', 10)); // v1

        // A racing insert reusing version 1 must fail the (document_id, version_number) unique index.
        $this->expectException(QueryException::class);
        DocumentVersion::create(['document_id' => $doc->id, 'version_number' => 1, 'path' => 'dup', 'original_filename' => 'dup']);
    }
}
