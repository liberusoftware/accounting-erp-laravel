<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\Leases\Models\Lease;

final class LeaseActivated
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly Lease $lease) {}
}
