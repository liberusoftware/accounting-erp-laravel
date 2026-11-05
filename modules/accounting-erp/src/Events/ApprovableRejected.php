<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Events;

use Illuminate\Database\Eloquent\Model;

final readonly class ApprovableRejected
{
    public function __construct(
        public Model $approvable,
        public ?string $reason = null,
    ) {}
}
