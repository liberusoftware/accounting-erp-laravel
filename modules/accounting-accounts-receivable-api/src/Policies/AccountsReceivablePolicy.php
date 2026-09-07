<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class AccountsReceivablePolicy
{
    private function can(?Authenticatable $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    public function viewAny(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.receivables.read');
    }

    public function view(?Authenticatable $user, object $model): bool
    {
        return $this->can($user, 'accounting.receivables.read');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.receivables.write');
    }

    public function update(?Authenticatable $user, object $model): bool
    {
        return $this->can($user, 'accounting.receivables.write');
    }
}
