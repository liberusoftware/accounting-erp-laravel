<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class AccountingSettingSaved
{
    use Dispatchable;

    public function __construct(public Model $setting) {}
}
