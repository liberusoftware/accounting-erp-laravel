<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Events;

use Liberu\Accounting\TaxCore\Models\TaxEvidence;

final readonly class TaxEvidenceCaptured
{
    public function __construct(public TaxEvidence $evidence) {}
}
