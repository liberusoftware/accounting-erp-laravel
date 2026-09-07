<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPaymentsApi\Policies;

final class BillPaymentsPolicy
{
    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    public function viewAny(?object $user = null): bool
    {
        return $this->can($user, 'accounting.bill-payments.read');
    }

    public function view(?object $user, object $payment): bool
    {
        return $this->can($user, 'accounting.bill-payments.read');
    }

    public function create(?object $user = null): bool
    {
        return $this->can($user, 'accounting.bill-payments.write');
    }

    public function update(?object $user, object $payment): bool
    {
        return $this->can($user, 'accounting.bill-payments.write');
    }
}
