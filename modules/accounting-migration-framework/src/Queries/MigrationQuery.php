<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\MigrationFramework\Models\MigrationBatch;
use Liberu\Accounting\MigrationFramework\Models\MigrationSource;

final class MigrationQuery
{
    public function sources(?int $teamId = null, int $perPage = 25): LengthAwarePaginator
    {
        $q = MigrationSource::query()->latest();
        if ($teamId !== null) {
            $q->where('team_id', $teamId);
        }

        return $q->paginate(min(max($perPage, 1), 100));
    }

    public function batches(?int $teamId = null, int $perPage = 25): LengthAwarePaginator
    {
        $q = MigrationBatch::query()->withCount(['rows', 'attachments'])->latest();
        if ($teamId !== null) {
            $q->where('team_id', $teamId);
        }

        return $q->paginate(min(max($perPage, 1), 100));
    }

    public function counts(MigrationBatch $batch): array
    {
        return ['total' => $batch->total_count, 'processed' => $batch->processed_count, 'success' => $batch->success_count, 'errors' => $batch->error_count, 'skipped' => $batch->skipped_count, 'remaining' => max(0, $batch->total_count - $batch->processed_count)];
    }
}
