<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class AccountingCorePolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.core.read');
    }

    public function view(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.core.read');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.core.write');
    }

    public function update(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.core.write');
    }

    public function delete(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.core.write');
    }

    private function can(?Authenticatable $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
