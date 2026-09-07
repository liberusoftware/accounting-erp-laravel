<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\DocumentCapture\Actions\ArchiveDocument;
use Liberu\Accounting\DocumentCapture\Actions\ExtractDocument;
use Liberu\Accounting\DocumentCapture\Actions\ReviewDocument;
use Liberu\Accounting\DocumentCapture\Actions\UploadDocument;
use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Events\CaptureStatusChanged;
use Liberu\Accounting\DocumentCapture\Exceptions\InvalidCapture;
use Tests\TestCase;

final class AccountingDocumentCaptureTest extends TestCase
{
    use RefreshDatabase;

    public function test_capture_lifecycle_records_extraction_review_and_archival(): void
    {
        Event::fake();
        $document = app(UploadDocument::class)->handle([
            'team_id' => 1,
            'source_channel' => 'mobile',
            'file_ref' => 'uploads/invoice.pdf',
            'checksum' => 'checksum-001',
            'mime_type' => 'application/pdf',
        ]);
        $document = app(ExtractDocument::class)->handle($document, [
            'invoice_number' => 'INV-001',
            'lines' => [['description' => 'Service', 'amount' => 100]],
        ], 0.93, 'ocr', 'operator');
        $document = app(ReviewDocument::class)->handle($document, true, null, 'reviewer');
        $document = app(ArchiveDocument::class)->handle($document, 'archiver');

        $this->assertSame(CaptureStatus::Archived, $document->status);
        $this->assertSame(['uploaded', 'extracted', 'approved', 'archived'], $document->events()->orderBy('id')->pluck('event')->all());
        Event::assertDispatchedTimes(CaptureStatusChanged::class, 3);
    }

    public function test_extraction_requires_invoice_lines(): void
    {
        $document = app(UploadDocument::class)->handle([
            'team_id' => 1,
            'source_channel' => 'email',
            'file_ref' => 'uploads/invoice-002.pdf',
            'checksum' => 'checksum-002',
            'mime_type' => 'application/pdf',
        ]);

        $this->expectException(InvalidCapture::class);
        app(ExtractDocument::class)->handle($document, ['invoice_number' => 'INV-002'], 0.8, 'ocr');
    }

    public function test_api_write_scope_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['accounting.document-capture.read']);
        $this->postJson('/api/v1/accounting/document-capture', [])->assertForbidden();
    }
}
