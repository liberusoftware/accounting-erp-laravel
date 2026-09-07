<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;

final readonly class OpenItemCreated implements ShouldDispatchAfterCommit
{
    public function __construct(public PayableOpenItem $openItem) {}
}
