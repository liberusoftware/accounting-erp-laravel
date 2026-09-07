<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccounts\Events;

use Liberu\Accounting\BankAccounts\Models\BankAccount;

final readonly class BankAccountStatusChanged
{
    public function __construct(public BankAccount $account, public string $previousStatus) {}
}
