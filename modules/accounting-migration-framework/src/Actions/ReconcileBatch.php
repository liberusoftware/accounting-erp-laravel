<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MigrationFramework\Enums\MigrationStatus;
use Liberu\Accounting\MigrationFramework\Enums\RowStatus;
use Liberu\Accounting\MigrationFramework\Exceptions\InvalidMigration;
use Liberu\Accounting\MigrationFramework\Models\MigrationBatch;
use Liberu\Accounting\MigrationFramework\Models\MigrationReconciliation;

final class ReconcileBatch
{
    public function handle(MigrationBatch $batch, array $attributes = []): MigrationReconciliation
    {
        if ($batch->status !== MigrationStatus::Completed) {
            throw new InvalidMigration('Only completed batches can be reconciled.');
        }

        return DB::transaction(function () use ($batch, $attributes): MigrationReconciliation {
            $failed = $batch->rows()->where('status', RowStatus::Failed)->count();
            $imported = $batch->rows()->where('status', RowStatus::Imported)->count();
            $sourceCount = $batch->total_count;
            $sourceTotal = isset($attributes['source_total']) ? (float) $attributes['source_total'] : null;
            $destinationTotal = isset($attributes['destination_total']) ? (float) $attributes['destination_total'] : null;
            $variance = $sourceTotal !== null && $destinationTotal !== null ? round($destinationTotal - $sourceTotal, 2) : null;
            $status = $failed === 0 && ($variance === null || abs($variance) < 0.0001) ? 'matched' : 'variance';
            $recon = MigrationReconciliation::updateOrCreate(['batch_id' => $batch->id], ['source_count' => $sourceCount, 'imported_count' => $imported, 'failed_count' => $failed, 'source_total' => $sourceTotal, 'destination_total' => $destinationTotal, 'variance' => $variance, 'status' => $status, 'notes' => $attributes['notes'] ?? null, 'metadata' => $attributes['metadata'] ?? null]);
            $batch->update(['status' => $status === 'matched' ? MigrationStatus::Reconciled : MigrationStatus::Completed]);

            return $recon;
        });
    }
}
