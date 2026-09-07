<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Actions;

use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Events\CaptureStatusChanged;
use Liberu\Accounting\DocumentCapture\Exceptions\InvalidCapture;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

final class ExtractDocument
{
    public function handle(CapturedDocument $d, array $extracted, float $confidence, string $adapter, ?string $actor = null): CapturedDocument
    {
        if ($d->status !== CaptureStatus::Uploaded) {
            throw new InvalidCapture('Only uploaded documents can be extracted.');
        }if ($confidence < 0 || $confidence > 1 || blank($adapter) || ! isset($extracted['lines']) || ! is_array($extracted['lines']) || $extracted['lines'] === []) {
            throw new InvalidCapture('Extraction confidence, adapter, or line data is invalid.');
        }$d->update(['status' => CaptureStatus::Extracted, 'extracted_data' => $extracted, 'confidence' => $confidence]);
        $d->events()->create(['event' => 'extracted', 'adapter_ref' => $adapter, 'actor_ref' => $actor]);
        $d = $d->refresh();
        event(new CaptureStatusChanged($d, 'extracted', $actor));

        return $d;
    }
}
