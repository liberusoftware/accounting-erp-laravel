<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\XeroMigration\Enums\MigrationRecordStatus;
use Liberu\Accounting\XeroMigration\Exceptions\InvalidXeroMigration;
use Liberu\Accounting\XeroMigration\Models\XeroConnection;
use Liberu\Accounting\XeroMigration\Models\XeroMigrationRecord;

final class RecordMigration
{
    public function handle(XeroConnection $connection, array $attributes): XeroMigrationRecord
    {
        if (blank($attributes['source_type'] ?? null) || blank($attributes['source_id'] ?? null)) {
            throw new InvalidXeroMigration('A source type and source identifier are required.');
        }

        return DB::transaction(fn (): XeroMigrationRecord => XeroMigrationRecord::updateOrCreate(['connection_id' => $connection->id, 'source_type' => $attributes['source_type'], 'source_id' => $attributes['source_id']], array_merge($attributes, ['team_id' => $connection->team_id, 'status' => $attributes['status'] ?? MigrationRecordStatus::Migrated])));
    }
}
