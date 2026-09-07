<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCodingApi\Policies;

use Liberu\Accounting\CashCoding\Models\CashCodingBatch;

final class CashCodingPolicy
{
    public function viewAny($user): bool
    {
        return $user !== null && $user->tokenCan('accounting.cash-coding.read');
    }

    public function view($user, CashCodingBatch $batch): bool
    {
        return $this->viewAny($user) && (int) $batch->team_id === (int) $user->current_team_id;
    }

    public function create($user): bool
    {
        return $user !== null && $user->tokenCan('accounting.cash-coding.write');
    }

    public function update($user, CashCodingBatch $batch): bool
    {
        return $this->create($user) && (int) $batch->team_id === (int) $user->current_team_id;
    }
}
