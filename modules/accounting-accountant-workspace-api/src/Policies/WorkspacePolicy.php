<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceApi\Policies;

final class WorkspacePolicy
{
    public function viewAny(?object $user): bool
    {
        return $this->can($user, 'accounting.accountant-workspace.read');
    }

    public function create(?object $user): bool
    {
        return $this->can($user, 'accounting.accountant-workspace.write') && ($user->current_team_id ?? null) !== null;
    }

    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
