<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionApi\Policies;

final class AnomalyPolicy
{
    public function viewAny(?object $user): bool
    {
        return $this->can($user, 'accounting.anomaly-detection.read');
    }

    public function update(?object $user, object $record): bool
    {
        return $this->can($user, 'accounting.anomaly-detection.write') && (int) $record->team_id === (int) ($user->current_team_id ?? 0);
    }

    private function can(?object $user, string $ability): bool
    {
        return $user !== null && method_exists($user, 'tokenCan') && $user->tokenCan($ability);
    }
}
