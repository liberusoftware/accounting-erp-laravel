<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Liberu\Accounting\Dimensions\Models\Dimension;

final class DimensionsPolicy
{
    private function can(?Authenticatable $u, string $ability): bool
    {
        return $u !== null && method_exists($u, 'tokenCan') && $u->tokenCan($ability);
    }

    public function viewAny(?Authenticatable $u): bool
    {
        return $this->can($u, 'accounting.dimensions.read');
    }

    public function view(?Authenticatable $u, Dimension $d): bool
    {
        return $this->can($u, 'accounting.dimensions.read');
    }

    public function create(?Authenticatable $u): bool
    {
        return $this->can($u, 'accounting.dimensions.write');
    }

    public function update(?Authenticatable $u, Dimension $d): bool
    {
        return $this->can($u, 'accounting.dimensions.write');
    }

    public function delete(?Authenticatable $u, Dimension $d): bool
    {
        return $this->can($u, 'accounting.dimensions.write');
    }
}
