<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Events;

use Liberu\Accounting\ThreeWayMatching\Models\MatchException;

final readonly class MatchExceptionResolved
{
    public function __construct(public MatchException $exception) {}
}
