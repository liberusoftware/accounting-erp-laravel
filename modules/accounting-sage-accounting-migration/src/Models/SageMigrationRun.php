<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\SageAccountingMigration\Enums\MigrationRunStatus;

/**
 * @property int $id
 * @property int|null $connection_id
 * @property MigrationRunStatus $status
 * @property int $total_records
 * @property int $imported_records
 * @property int $failed_records
 * @property array<string,mixed>|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $finished_at
 */
final class SageMigrationRun extends Model
{
    protected $table = 'accounting_sage_migration_runs';

    protected $fillable = ['connection_id', 'status', 'total_records', 'imported_records', 'failed_records', 'metadata', 'started_at', 'finished_at'];

    protected $casts = ['status' => MigrationRunStatus::class, 'metadata' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime'];

    /** @return HasMany<SageMigrationRecord, $this> */
    public function records(): HasMany
    {
        return $this->hasMany(SageMigrationRecord::class, 'run_id');
    }
}
