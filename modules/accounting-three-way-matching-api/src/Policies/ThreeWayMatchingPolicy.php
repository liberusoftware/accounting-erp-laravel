<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatchingApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class ThreeWayMatchingPolicy
{
    private function can(?Authenticatable $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    public function viewAny(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.three-way-matching.read');
    }

    public function view(?Authenticatable $user, object $match): bool
    {
        return $this->can($user, 'accounting.three-way-matching.read');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.three-way-matching.write');
    }

    public function update(?Authenticatable $user, object $match): bool
    {
        return $this->can($user, 'accounting.three-way-matching.write');
    }
}
