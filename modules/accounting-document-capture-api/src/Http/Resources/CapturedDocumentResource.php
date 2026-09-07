<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCaptureApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

/** @mixin CapturedDocument */ final class CapturedDocumentResource extends JsonResource
{
    public function toArray(Request $r): array
    {
        $d = $this->resource;

        return ['id' => $d->getKey(), 'source_channel' => $d->source_channel, 'file_ref' => $d->file_ref, 'checksum' => $d->checksum, 'mime_type' => $d->mime_type, 'status' => $d->status->value, 'supplier_ref' => $d->supplier_ref, 'document_ref' => $d->document_ref, 'extracted_data' => $d->extracted_data, 'confidence' => $d->confidence, 'duplicate_of' => $d->duplicate_of, 'retention_until' => $d->retention_until?->toDateString(), 'rejection_reason' => $d->rejection_reason, 'events' => $this->whenLoaded('events')];
    }
}
