<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableApi\Policies;

final class AccountingAccountsPayablePolicy
{
    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    public function viewAny(?object $user = null): bool
    {
        return $this->can($user, 'accounting.payables.read');
    }

    public function view(?object $user, object $item): bool
    {
        return $this->can($user, 'accounting.payables.read');
    }

    public function create(?object $user = null): bool
    {
        return $this->can($user, 'accounting.payables.write');
    }

    public function update(?object $user, object $item): bool
    {
        return $this->can($user, 'accounting.payables.write');
    }
}
