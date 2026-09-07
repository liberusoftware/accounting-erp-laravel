<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

final readonly class EstimateLifecycleChanged implements ShouldDispatchAfterCommit
{
    public function __construct(public Estimate $estimate, public string $event, public ?string $actorReference = null) {}
}
