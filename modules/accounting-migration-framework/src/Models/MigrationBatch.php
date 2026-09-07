<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\MigrationFramework\Enums\MigrationStatus;

/**
 * @property MigrationStatus $status
 * @property bool $dry_run
 * @property int $total_count
 * @property int $processed_count
 * @property int $success_count
 * @property int $error_count
 * @property int $skipped_count
 */
final class MigrationBatch extends Model
{
    protected $table = 'accounting_migration_batches';

    protected $fillable = ['team_id', 'batch_ref', 'source_id', 'mapping_id', 'status', 'dry_run', 'resume_token', 'total_count', 'processed_count', 'success_count', 'error_count', 'skipped_count', 'started_at', 'completed_at', 'paused_at', 'failure_message', 'metadata'];

    protected $casts = ['status' => MigrationStatus::class, 'dry_run' => 'boolean', 'total_count' => 'integer', 'processed_count' => 'integer', 'success_count' => 'integer', 'error_count' => 'integer', 'skipped_count' => 'integer', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'paused_at' => 'datetime', 'metadata' => 'array'];

    /** @return BelongsTo<MigrationMapping, $this> */
    public function mapping(): BelongsTo
    {
        return $this->belongsTo(MigrationMapping::class, 'mapping_id');
    }

    /** @return BelongsTo<MigrationSource, $this> */
    public function source(): BelongsTo
    {
        return $this->belongsTo(MigrationSource::class, 'source_id');
    }

    /** @return HasMany<MigrationRow, $this> */
    public function rows(): HasMany
    {
        return $this->hasMany(MigrationRow::class, 'batch_id');
    }

    /** @return HasMany<MigrationAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(MigrationAttachment::class, 'batch_id');
    }

    /** @return HasMany<MigrationReconciliation, $this> */
    public function reconciliation(): HasMany
    {
        return $this->hasMany(MigrationReconciliation::class, 'batch_id');
    }
}
