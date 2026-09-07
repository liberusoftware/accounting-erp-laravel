<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Liberu\Accounting\ChartOfAccounts\Models\Account;

final class AccountPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.chart.read');
    }

    public function view(?Authenticatable $user, Account $account): bool
    {
        return $this->can($user, 'accounting.chart.read');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.chart.write');
    }

    public function update(?Authenticatable $user, Account $account): bool
    {
        return $this->can($user, 'accounting.chart.write');
    }

    public function delete(?Authenticatable $user, Account $account): bool
    {
        return $this->can($user, 'accounting.chart.write');
    }

    private function can(?Authenticatable $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
