<?php

declare(strict_types=1);

namespace Liberu\Accounting\PeriodsApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class AccountingPeriodsPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.periods.read');
    }

    public function view(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.periods.read');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.periods.write');
    }

    public function update(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.periods.write');
    }

    private function can(?Authenticatable $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
