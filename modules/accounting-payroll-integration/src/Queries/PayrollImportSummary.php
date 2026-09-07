<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegration\Queries;

use Liberu\Accounting\PayrollIntegration\Models\PayrollImport;

final class PayrollImportSummary
{
    /** @return array<string,mixed> */
    public function forTeam(?int $teamId = null): array
    {
        $rows = PayrollImport::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->get();

        return ['count' => $rows->count(), 'providers' => $rows->groupBy('provider')->map->count()->all(), 'validated' => $rows->where('status', 'validated')->count(), 'failed' => $rows->where('status', 'failed')->count(), 'imported' => $rows->where('status', 'imported')->count(), 'reconciled' => $rows->where('status', 'reconciled')->count()];
    }
}
