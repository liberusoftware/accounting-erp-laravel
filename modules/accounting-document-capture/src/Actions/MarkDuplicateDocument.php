<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Actions;

use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Exceptions\InvalidCapture;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

final class MarkDuplicateDocument
{
    public function handle(CapturedDocument $d, CapturedDocument $original, ?string $actor = null): CapturedDocument
    {
        if ($d->is($original) || $d->team_id !== $original->team_id || $original->status === CaptureStatus::Archived) {
            throw new InvalidCapture('A duplicate must reference another active document in the same team.');
        }$d->update(['duplicate_of' => $original->getKey(), 'status' => CaptureStatus::Rejected, 'rejection_reason' => 'Duplicate source document']);
        $d->events()->create(['event' => 'duplicate_marked', 'actor_ref' => $actor, 'metadata' => ['original_id' => $original->getKey()]]);

        return $d->refresh();
    }
}
