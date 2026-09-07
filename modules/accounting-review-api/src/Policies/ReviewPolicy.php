<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReviewApi\Policies;

final class ReviewPolicy
{
    public function viewAny(?object $user): bool
    {
        return $this->can($user, 'accounting.review.read');
    }

    public function create(?object $user): bool
    {
        return $this->can($user, 'accounting.review.write') && ($user->current_team_id ?? null) !== null;
    }

    public function view(?object $user, object $record): bool
    {
        return $this->can($user, 'accounting.review.read') && (int) $record->team_id === (int) ($user->current_team_id ?? 0);
    }

    public function update(?object $user, object $record): bool
    {
        return $this->can($user, 'accounting.review.write') && (int) $record->team_id === (int) ($user->current_team_id ?? 0);
    }

    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
