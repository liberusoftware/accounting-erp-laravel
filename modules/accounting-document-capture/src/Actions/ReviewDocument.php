<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Actions;

use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Events\CaptureStatusChanged;
use Liberu\Accounting\DocumentCapture\Exceptions\InvalidCapture;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

final class ReviewDocument
{
    public function handle(CapturedDocument $d, bool $approved, ?string $reason = null, ?string $actor = null): CapturedDocument
    {
        if ($d->status !== CaptureStatus::Extracted) {
            throw new InvalidCapture('Only extracted documents can be reviewed.');
        }if (! $approved && blank($reason)) {
            throw new InvalidCapture('Rejected capture requires a reason.');
        }$event = $approved ? 'approved' : 'rejected';
        $d->update(['status' => $approved ? CaptureStatus::Approved : CaptureStatus::Rejected, 'reviewed_by' => $actor, 'reviewed_at' => now(), 'rejection_reason' => $approved ? null : $reason]);
        $d->events()->create(['event' => $event, 'actor_ref' => $actor, 'message' => $reason]);
        $d = $d->refresh();
        event(new CaptureStatusChanged($d, $event, $actor));

        return $d;
    }
}
