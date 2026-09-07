<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRulesApi\Policies;

use Liberu\Accounting\BankRules\Models\BankRule;

final class BankRulesPolicy
{
    public function viewAny($user): bool
    {
        return $user !== null && $user->tokenCan('accounting.bank-rules.read');
    }

    public function view($user, BankRule $rule): bool
    {
        return $this->viewAny($user) && (int) $rule->team_id === (int) $user->current_team_id;
    }

    public function create($user): bool
    {
        return $user !== null && $user->tokenCan('accounting.bank-rules.write');
    }

    public function update($user, BankRule $rule): bool
    {
        return $this->create($user) && (int) $rule->team_id === (int) $user->current_team_id;
    }

    public function delete($user, BankRule $rule): bool
    {
        return $this->update($user, $rule);
    }
}
