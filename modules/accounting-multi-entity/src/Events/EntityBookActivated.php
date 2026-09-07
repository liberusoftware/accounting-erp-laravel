<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Events;

use Liberu\Accounting\MultiEntity\Models\EntityBook;

final class EntityBookActivated
{
    public function __construct(public readonly EntityBook $entity) {}
}
