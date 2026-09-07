<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsApi\Policies;

final class BusinessInsightsPolicy
{
    public function viewAny(?object $user): bool
    {
        return $this->can($user, 'accounting.business-insights.read');
    }

    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
