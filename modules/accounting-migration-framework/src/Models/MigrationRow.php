<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\MigrationFramework\Enums\RowStatus;

/**
 * @property RowStatus $status
 * @property array<string,mixed> $payload
 * @property int $attempts
 */
final class MigrationRow extends Model
{
    protected $table = 'accounting_migration_rows';

    protected $fillable = ['batch_id', 'row_ref', 'source_key', 'destination_key', 'payload', 'transformed_payload', 'status', 'error_code', 'error_message', 'attempts', 'processed_at', 'metadata'];

    protected $casts = ['payload' => 'array', 'transformed_payload' => 'array', 'status' => RowStatus::class, 'attempts' => 'integer', 'processed_at' => 'datetime', 'metadata' => 'array'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(MigrationBatch::class, 'batch_id');
    }
}
