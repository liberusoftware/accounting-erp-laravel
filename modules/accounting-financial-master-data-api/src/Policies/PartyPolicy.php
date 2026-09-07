<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class PartyPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.master-data.read');
    }

    public function view(?Authenticatable $user, Party $party): bool
    {
        return $this->can($user, 'accounting.master-data.read');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->can($user, 'accounting.master-data.write');
    }

    public function update(?Authenticatable $user, Party $party): bool
    {
        return $this->can($user, 'accounting.master-data.write');
    }

    public function delete(?Authenticatable $user, Party $party): bool
    {
        return $this->can($user, 'accounting.master-data.write');
    }

    private function can(?Authenticatable $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
