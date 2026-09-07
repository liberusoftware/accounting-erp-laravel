<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingApi\Policies;

final class CarbonAccountingPolicy
{
    public function viewAny(?object $user): bool
    {
        return $this->can($user, 'accounting.carbon-accounting.read');
    }

    public function create(?object $user): bool
    {
        return $this->can($user, 'accounting.carbon-accounting.write') && ($user->current_team_id ?? null) !== null;
    }

    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
