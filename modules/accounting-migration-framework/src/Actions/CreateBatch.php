<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MigrationFramework\Enums\MigrationStatus;
use Liberu\Accounting\MigrationFramework\Exceptions\InvalidMigration;
use Liberu\Accounting\MigrationFramework\Models\MigrationBatch;
use Liberu\Accounting\MigrationFramework\Models\MigrationMapping;
use Liberu\Accounting\MigrationFramework\Models\MigrationSource;

final class CreateBatch
{
    public function handle(MigrationSource $source, MigrationMapping $mapping, array $attributes, array $rows = []): MigrationBatch
    {
        $ref = trim((string) ($attributes['batch_ref'] ?? ''));
        if ($ref === '' || $mapping->source_id !== $source->id) {
            throw new InvalidMigration('A batch requires a reference and a mapping belonging to its source.');
        }$hash = hash('sha256', json_encode($rows, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        return DB::transaction(function () use ($source, $mapping, $attributes, $rows, $ref, $hash): MigrationBatch {
            $existing = MigrationBatch::query()->where(['team_id' => $attributes['team_id'] ?? null, 'batch_ref' => $ref])->first();
            if ($existing) {
                if (($existing->metadata['rows_hash'] ?? null) !== $hash) {
                    throw new InvalidMigration('Batch reference already exists with different rows.');
                }

                return $existing->load('rows');
            }$batch = MigrationBatch::create(['team_id' => $attributes['team_id'] ?? null, 'batch_ref' => $ref, 'source_id' => $source->id, 'mapping_id' => $mapping->id, 'status' => MigrationStatus::Draft, 'dry_run' => (bool) ($attributes['dry_run'] ?? false), 'resume_token' => bin2hex(random_bytes(24)), 'total_count' => count($rows), 'metadata' => array_merge($attributes['metadata'] ?? [], ['rows_hash' => $hash])]);
            foreach ($rows as $index => $row) {
                if (! is_array($row) || blank($row['source_key'] ?? null)) {
                    throw new InvalidMigration('Every migration row requires a source key.');
                }$batch->rows()->create(['row_ref' => (string) ($row['row_ref'] ?? $index + 1), 'source_key' => (string) $row['source_key'], 'payload' => $row['payload'] ?? $row]);
            }

            return $batch->load('rows');
        });
    }
}
