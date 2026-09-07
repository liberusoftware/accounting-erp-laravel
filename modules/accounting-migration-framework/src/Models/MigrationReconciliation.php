<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MigrationReconciliation extends Model
{
    protected $table = 'accounting_migration_reconciliations';

    protected $fillable = ['batch_id', 'source_count', 'imported_count', 'failed_count', 'source_total', 'destination_total', 'variance', 'status', 'notes', 'metadata'];

    protected $casts = ['source_count' => 'integer', 'imported_count' => 'integer', 'failed_count' => 'integer', 'source_total' => 'decimal:2', 'destination_total' => 'decimal:2', 'variance' => 'decimal:2', 'metadata' => 'array'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(MigrationBatch::class, 'batch_id');
    }
}
