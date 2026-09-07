<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Events;

use Liberu\Accounting\MultiCurrency\Models\ExchangeRate;

final class RateRecorded
{
    public function __construct(public readonly ExchangeRate $rate) {}
}
