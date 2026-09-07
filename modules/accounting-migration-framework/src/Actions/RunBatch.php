<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MigrationFramework\Enums\MigrationStatus;
use Liberu\Accounting\MigrationFramework\Enums\RowStatus;
use Liberu\Accounting\MigrationFramework\Events\MigrationBatchCompleted;
use Liberu\Accounting\MigrationFramework\Exceptions\InvalidMigration;
use Liberu\Accounting\MigrationFramework\Models\MigrationBatch;
use Liberu\Accounting\MigrationFramework\Models\MigrationRow;

final class RunBatch
{
    public function handle(MigrationBatch $batch, bool $dryRun = false, int $limit = 100): MigrationBatch
    {
        if ($batch->status === MigrationStatus::Reconciled) {
            return $batch;
        }if ($limit < 1) {
            throw new InvalidMigration('Batch limit must be positive.');
        }

        return DB::transaction(function () use ($batch, $dryRun, $limit): MigrationBatch {
            $batch = MigrationBatch::query()->lockForUpdate()->findOrFail($batch->id);
            $isDry = $dryRun || $batch->dry_run;
            if (! $isDry && $batch->status === MigrationStatus::DryRun) {
                $batch->rows()->where('status', RowStatus::Valid)->update(['status' => RowStatus::Pending, 'processed_at' => null]);
                $batch->update(['processed_count' => 0, 'success_count' => 0, 'error_count' => 0]);
            }$batch->update(['status' => $isDry ? MigrationStatus::DryRun : MigrationStatus::Running, 'started_at' => $batch->started_at ?? now()]);
            foreach ($batch->rows()->whereIn('status', [RowStatus::Pending, RowStatus::Failed])->orderBy('id')->limit($limit)->get() as $row) {
                $this->process($row, $batch, $isDry);
            }$batch->refresh();
            if ($batch->processed_count >= $batch->total_count) {
                if ($isDry) {
                    return $batch->refresh();
                }$batch->update(['status' => MigrationStatus::Completed, 'completed_at' => now()]);
                $result = $batch->refresh();
                DB::afterCommit(fn () => event(new MigrationBatchCompleted($result)));

                return $result;
            }

            return $batch;
        });
    }

    private function process(MigrationRow $row, MigrationBatch $batch, bool $dryRun): void
    {
        $payload = $row->payload;
        if ($payload === []) {
            $row->update(['status' => RowStatus::Failed, 'error_code' => 'invalid_payload', 'error_message' => 'Payload is empty.', 'attempts' => $row->attempts + 1, 'processed_at' => now()]);
            $batch->increment('error_count');
        } else {
            $transformed = $this->transform($payload, $batch->mapping->transforms ?? []);
            $row->update(['transformed_payload' => $transformed, 'status' => $dryRun ? RowStatus::Valid : RowStatus::Imported, 'attempts' => $row->attempts + 1, 'processed_at' => now()]);
            $batch->increment('success_count');
        }$batch->increment('processed_count');
    }

    private function transform(array $payload, array $transforms): array
    {
        foreach ($transforms as $field => $transform) {
            if (($transform['operation'] ?? null) === 'uppercase' && array_key_exists($field, $payload)) {
                $payload[$field] = strtoupper((string) $payload[$field]);
            }if (($transform['operation'] ?? null) === 'trim' && array_key_exists($field, $payload)) {
                $payload[$field] = trim((string) $payload[$field]);
            }
        }

        return $payload;
    }
}
