<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCaptureApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\DocumentCapture\Actions\ArchiveDocument;
use Liberu\Accounting\DocumentCapture\Actions\ExtractDocument;
use Liberu\Accounting\DocumentCapture\Actions\MarkDuplicateDocument;
use Liberu\Accounting\DocumentCapture\Actions\ReviewDocument;
use Liberu\Accounting\DocumentCapture\Actions\UploadDocument;
use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;
use Liberu\Accounting\DocumentCapture\Queries\CaptureQuery;
use Liberu\Accounting\DocumentCaptureApi\Http\Resources\CapturedDocumentResource;

final class DocumentCaptureController extends Controller
{
    public function __construct(private readonly CaptureQuery $query) {}

    public function index(Request $r): mixed
    {
        $s = $r->filled('status') ? CaptureStatus::tryFrom($r->string('status')->toString()) : null;
        abort_if($r->filled('status') && $s === null, 422, 'Unknown capture status.');

        return CapturedDocumentResource::collection($this->query->paginate($r->integer('team_id') ?: null, $s, $r->integer('per_page', 25)));
    }

    public function store(Request $r, UploadDocument $a): CapturedDocumentResource
    {
        $d = $r->validate(['team_id' => 'nullable|integer', 'source_channel' => 'required|in:mobile,web,email', 'file_ref' => 'required|string|max:500', 'checksum' => 'required|string|max:255', 'mime_type' => 'required|string|max:100', 'retention_until' => 'nullable|date', 'metadata' => 'nullable|array']);
        $d['actor_ref'] = (string) $r->user()->getAuthIdentifier();

        return new CapturedDocumentResource($a->handle($d));
    }

    public function show(CapturedDocument $d): CapturedDocumentResource
    {
        return new CapturedDocumentResource($d->load('events'));
    }

    public function extract(Request $r, CapturedDocument $d, ExtractDocument $a): CapturedDocumentResource
    {
        $v = $r->validate(['extracted_data' => 'required|array', 'confidence' => 'required|numeric|min:0|max:1', 'adapter' => 'required|string|max:100']);

        return new CapturedDocumentResource($a->handle($d, $v['extracted_data'], (float) $v['confidence'], $v['adapter'], (string) $r->user()->getAuthIdentifier()));
    }

    public function review(Request $r, CapturedDocument $d, ReviewDocument $a): CapturedDocumentResource
    {
        $v = $r->validate(['approved' => 'required|boolean', 'reason' => 'nullable|string|max:5000']);

        return new CapturedDocumentResource($a->handle($d, (bool) $v['approved'], $v['reason'] ?? null, (string) $r->user()->getAuthIdentifier()));
    }

    public function duplicate(Request $r, CapturedDocument $d, MarkDuplicateDocument $a): CapturedDocumentResource
    {
        $v = $r->validate(['original_id' => 'required|integer']);
        $original = CapturedDocument::query()->findOrFail($v['original_id']);

        return new CapturedDocumentResource($a->handle($d, $original, (string) $r->user()->getAuthIdentifier()));
    }

    public function archive(Request $r, CapturedDocument $d, ArchiveDocument $a): CapturedDocumentResource
    {
        return new CapturedDocumentResource($a->handle($d, (string) $r->user()->getAuthIdentifier()));
    }
}
