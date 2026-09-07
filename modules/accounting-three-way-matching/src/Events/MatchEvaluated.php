<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Events;

use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;

final readonly class MatchEvaluated
{
    public function __construct(public MatchRecord $match) {}
}
