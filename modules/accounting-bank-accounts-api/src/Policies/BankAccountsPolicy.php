<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsApi\Policies;

final class BankAccountsPolicy
{
    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    public function viewAny(?object $user = null): bool
    {
        return $this->can($user, 'accounting.bank-accounts.read');
    }

    public function view(?object $user, object $account): bool
    {
        return $this->can($user, 'accounting.bank-accounts.read');
    }

    public function create(?object $user = null): bool
    {
        return $this->can($user, 'accounting.bank-accounts.write');
    }

    public function update(?object $user, object $account): bool
    {
        return $this->can($user, 'accounting.bank-accounts.write');
    }
}
