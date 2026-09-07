<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsApi\Policies;

final class BudgetsPolicy
{
    public function viewAny(?object $user): bool
    {
        return $this->can($user, 'accounting.budgets.read');
    }

    public function view(?object $user, object $record): bool
    {
        return $this->can($user, 'accounting.budgets.read') && (int) $record->team_id === (int) ($user->current_team_id ?? 0);
    }

    public function create(?object $user): bool
    {
        return $this->can($user, 'accounting.budgets.write');
    }

    public function update(?object $user, object $record): bool
    {
        return $this->can($user, 'accounting.budgets.write') && (int) $record->team_id === (int) ($user->current_team_id ?? 0);
    }

    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
