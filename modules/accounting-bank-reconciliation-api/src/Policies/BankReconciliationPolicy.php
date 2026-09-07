<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliationApi\Policies;

final class BankReconciliationPolicy
{
    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    public function viewAny(?object $user = null): bool
    {
        return $this->can($user, 'accounting.bank-reconciliation.read');
    }

    public function view(?object $user, object $session): bool
    {
        return $this->can($user, 'accounting.bank-reconciliation.read');
    }

    public function create(?object $user = null): bool
    {
        return $this->can($user, 'accounting.bank-reconciliation.write');
    }

    public function update(?object $user, object $session): bool
    {
        return $this->can($user, 'accounting.bank-reconciliation.write');
    }
}
