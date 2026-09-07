<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\QuickBooksOnlineMigration\Enums\MigrationStatus;
use Liberu\Accounting\QuickBooksOnlineMigration\Enums\RecordStatus;
use Liberu\Accounting\QuickBooksOnlineMigration\Exceptions\InvalidMigration;
use Liberu\Accounting\QuickBooksOnlineMigration\Models\QboMigrationRecord;
use Liberu\Accounting\QuickBooksOnlineMigration\Models\QboMigrationRun;

final class ImportMigrationRecords
{
    public function handle(QboMigrationRun $run, array $records): QboMigrationRun
    {
        return DB::transaction(function () use ($run, $records): QboMigrationRun {
            $run->update(['status' => MigrationStatus::Running, 'total_records' => count($records), 'started_at' => now()]);
            foreach ($records as $record) {
                if (! is_array($record) || blank($record['entity_type'] ?? null) || blank($record['source_id'] ?? null) || ! is_array($record['payload'] ?? null)) {
                    throw new InvalidMigration('Each record requires entity_type, source_id, and payload.');
                }$payload = $record['payload'];
                QboMigrationRecord::updateOrCreate(['run_id' => $run->id, 'entity_type' => $record['entity_type'], 'source_id' => (string) $record['source_id']], ['status' => RecordStatus::Imported, 'payload' => $payload, 'payload_hash' => hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)), 'metadata' => $record['metadata'] ?? null]);
            }$run->update(['status' => MigrationStatus::Completed, 'imported_records' => count($records), 'finished_at' => now()]);

            return $run->refresh();
        });
    }
}
