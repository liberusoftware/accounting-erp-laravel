<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Exceptions\InvalidCapture;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

final class UploadDocument
{
    public function handle(array $a): CapturedDocument
    {
        foreach (['source_channel', 'file_ref', 'checksum', 'mime_type'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidCapture("Missing source field [{$k}].");
            }
        }if (! in_array($a['source_channel'], ['mobile', 'web', 'email'], true) || ! str_starts_with((string) $a['mime_type'], 'application/') && ! str_starts_with((string) $a['mime_type'], 'image/')) {
            throw new InvalidCapture('Source channel or MIME type is unsupported.');
        }if (CapturedDocument::query()->where('team_id', $a['team_id'] ?? null)->where('checksum', $a['checksum'])->exists()) {
            throw new InvalidCapture('The source document is a duplicate.');
        }

        return DB::transaction(function () use ($a): CapturedDocument {
            $d = CapturedDocument::create(['team_id' => $a['team_id'] ?? null, 'source_channel' => $a['source_channel'], 'file_ref' => $a['file_ref'], 'checksum' => $a['checksum'], 'mime_type' => $a['mime_type'], 'status' => CaptureStatus::Uploaded, 'retention_until' => $a['retention_until'] ?? null, 'metadata' => $a['metadata'] ?? null]);
            $d->events()->create(['event' => 'uploaded', 'actor_ref' => $a['actor_ref'] ?? null]);

            return $d->refresh();
        });
    }
}
