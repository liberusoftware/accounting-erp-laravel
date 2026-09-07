<?php

declare(strict_types=1);

namespace Liberu\Accounting\Policies\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\Accounting\Policies\Models\PolicyRule;

final readonly class PolicyRuleSaved
{
    use Dispatchable;

    public function __construct(public PolicyRule $rule) {}
}
