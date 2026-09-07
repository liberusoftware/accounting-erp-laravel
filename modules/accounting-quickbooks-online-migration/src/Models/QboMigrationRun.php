<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\QuickBooksOnlineMigration\Enums\MigrationStatus;

/** @property int $id @property int|null $connection_id @property MigrationStatus $status @property int $total_records @property int $imported_records @property int $failed_records @property array<string,mixed>|null $metadata */
final class QboMigrationRun extends Model
{
    protected $table = 'accounting_qbo_migration_runs';

    protected $fillable = ['connection_id', 'status', 'total_records', 'imported_records', 'failed_records', 'errors', 'metadata', 'started_at', 'finished_at'];

    protected $casts = ['status' => MigrationStatus::class, 'errors' => 'array', 'metadata' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime'];

    /** @return HasMany<QboMigrationRecord, $this> */
    public function records(): HasMany
    {
        return $this->hasMany(QboMigrationRecord::class, 'run_id');
    }
}
