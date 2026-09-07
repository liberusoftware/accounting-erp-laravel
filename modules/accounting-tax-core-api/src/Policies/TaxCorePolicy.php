<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreApi\Policies;

use Liberu\Accounting\TaxCore\Models\TaxRule;

final class TaxCorePolicy
{
    public function viewAny($user): bool
    {
        return (bool) $user?->tokenCan('accounting.tax-core.read');
    }

    public function view($user, TaxRule $rule): bool
    {
        return $this->viewAny($user);
    }

    public function create($user): bool
    {
        return (bool) $user?->tokenCan('accounting.tax-core.write');
    }

    public function update($user, TaxRule $rule): bool
    {
        return $this->create($user);
    }

    public function delete($user, TaxRule $rule): bool
    {
        return $this->create($user);
    }
}
