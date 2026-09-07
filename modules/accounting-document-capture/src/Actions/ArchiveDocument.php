<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Actions;

use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Events\CaptureStatusChanged;
use Liberu\Accounting\DocumentCapture\Exceptions\InvalidCapture;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

final class ArchiveDocument
{
    public function handle(CapturedDocument $d, ?string $actor = null): CapturedDocument
    {
        if (! in_array($d->status, [CaptureStatus::Approved, CaptureStatus::Rejected], true)) {
            throw new InvalidCapture('Only reviewed documents can be archived.');
        }$d->update(['status' => CaptureStatus::Archived]);
        $d->events()->create(['event' => 'archived', 'actor_ref' => $actor]);
        $d = $d->refresh();
        event(new CaptureStatusChanged($d, 'archived', $actor));

        return $d;
    }
}
