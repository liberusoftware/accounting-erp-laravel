<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Liberu\Accounting\Core\Models\LegalEntity;

final class LegalEntityPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return $this->hasAbility($user, 'accounting.core.read');
    }

    public function view(?Authenticatable $user, LegalEntity $entity): bool
    {
        return $this->hasAbility($user, 'accounting.core.read');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->hasAbility($user, 'accounting.core.write');
    }

    public function update(?Authenticatable $user, LegalEntity $entity): bool
    {
        return $this->hasAbility($user, 'accounting.core.write');
    }

    public function delete(?Authenticatable $user, LegalEntity $entity): bool
    {
        return $this->hasAbility($user, 'accounting.core.write');
    }

    private function hasAbility(?Authenticatable $user, string $ability): bool
    {
        if ($user === null) {
            return false;
        }

        return method_exists($user, 'tokenCan')
            && $user->tokenCan($ability);
    }
}
