<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class SupplierBillsPolicy
{
    private function can(?Authenticatable $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    public function viewAny(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.supplier-bills.read');
    }

    public function view(?Authenticatable $user, object $bill): bool
    {
        return $this->can($user, 'accounting.supplier-bills.read');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.supplier-bills.write');
    }

    public function update(?Authenticatable $user, object $bill): bool
    {
        return $this->can($user, 'accounting.supplier-bills.write');
    }
}
