<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

final readonly class CaptureStatusChanged implements ShouldDispatchAfterCommit
{
    public function __construct(public CapturedDocument $document, public string $event, public ?string $actorReference = null) {}
}
