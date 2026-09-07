<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final readonly class EInvoiceStatusChanged implements ShouldDispatchAfterCommit
{
    public function __construct(public EInvoiceDocument $document, public string $event, public ?string $actorReference = null) {}
}
