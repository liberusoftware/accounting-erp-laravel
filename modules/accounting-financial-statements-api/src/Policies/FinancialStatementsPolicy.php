<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatementsApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class FinancialStatementsPolicy
{
    public function view(?Authenticatable $user): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan('accounting.financial-statements.read');
    }
}
