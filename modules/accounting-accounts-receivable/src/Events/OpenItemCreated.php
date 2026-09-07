<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Events;

use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;

final class OpenItemCreated
{
    public function __construct(public readonly ReceivableOpenItem $openItem) {}
}
