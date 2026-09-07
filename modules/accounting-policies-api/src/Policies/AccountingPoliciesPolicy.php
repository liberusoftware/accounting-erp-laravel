<?php

namespace Liberu\Accounting\PoliciesApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Liberu\Accounting\Policies\Models\PolicyRule;

final class AccountingPoliciesPolicy
{
    public function viewAny(?Authenticatable $u): bool
    {
        return $this->can($u, 'accounting.policies.read');
    }

    public function view(?Authenticatable $u, PolicyRule $rule): bool
    {
        return $this->can($u, 'accounting.policies.read');
    }

    public function create(?Authenticatable $u): bool
    {
        return $this->can($u, 'accounting.policies.write');
    }

    public function update(?Authenticatable $u, PolicyRule $rule): bool
    {
        return $this->can($u, 'accounting.policies.write');
    }

    public function delete(?Authenticatable $u, PolicyRule $rule): bool
    {
        return $this->can($u, 'accounting.policies.write');
    }

    private function can(?Authenticatable $u, string $a): bool
    {
        return $u !== null && method_exists($u, 'tokenCan') && $u->tokenCan($a);
    }
}
