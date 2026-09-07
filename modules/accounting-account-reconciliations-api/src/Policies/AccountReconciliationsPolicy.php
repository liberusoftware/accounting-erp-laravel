<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsApi\Policies;

final class AccountReconciliationsPolicy
{
    public function viewAny(?object $user): bool
    {
        return $this->can($user, 'accounting.account-reconciliations.read');
    }

    public function view(?object $user, object $record): bool
    {
        return $this->can($user, 'accounting.account-reconciliations.read') && $this->belongsToCurrentTeam($user, $record);
    }

    public function create(?object $user): bool
    {
        return $this->can($user, 'accounting.account-reconciliations.write');
    }

    public function update(?object $user, object $record): bool
    {
        return $this->can($user, 'accounting.account-reconciliations.write') && $this->belongsToCurrentTeam($user, $record);
    }

    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    private function belongsToCurrentTeam(?object $user, object $record): bool
    {
        return $user !== null && (int) ($record->team_id ?? 0) === (int) ($user->current_team_id ?? $user->currentTeam?->getKey() ?? 0);
    }
}
