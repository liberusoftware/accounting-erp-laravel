<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Events;

use Illuminate\Database\Eloquent\Model;

final readonly class ApprovableApproved
{
    public function __construct(
        public Model $approvable,
    ) {}
}
