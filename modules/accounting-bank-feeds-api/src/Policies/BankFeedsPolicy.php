<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeedsApi\Policies;

final class BankFeedsPolicy
{
    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }

    public function viewAny(?object $user = null): bool
    {
        return $this->can($user, 'accounting.bank-feeds.read');
    }

    public function view(?object $user, object $connection): bool
    {
        return $this->can($user, 'accounting.bank-feeds.read');
    }

    public function create(?object $user = null): bool
    {
        return $this->can($user, 'accounting.bank-feeds.write');
    }

    public function update(?object $user, object $connection): bool
    {
        return $this->can($user, 'accounting.bank-feeds.write');
    }
}
