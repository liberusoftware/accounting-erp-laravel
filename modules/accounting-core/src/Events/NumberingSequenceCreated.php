<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\Accounting\Core\Models\NumberingSequence;

final readonly class NumberingSequenceCreated
{
    use Dispatchable;

    public function __construct(public NumberingSequence $sequence) {}
}
