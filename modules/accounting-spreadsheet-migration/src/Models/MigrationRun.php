<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\SpreadsheetMigration\Enums\MigrationMode;
use Liberu\Accounting\SpreadsheetMigration\Enums\MigrationStatus;

/**
 * @property int $id
 * @property int $template_id
 * @property MigrationMode $mode
 * @property MigrationStatus $status
 * @property int $row_count
 * @property float|string $source_total
 * @property float|string $target_total
 * @property array<string,mixed>|null $errors
 * @property-read MigrationTemplate $template
 */
final class MigrationRun extends Model
{
    protected $table = 'accounting_spreadsheet_migration_runs';

    protected $fillable = ['template_id', 'mode', 'status', 'source_hash', 'row_count', 'source_total', 'target_total', 'errors', 'metadata'];

    protected $casts = ['mode' => MigrationMode::class, 'status' => MigrationStatus::class, 'source_total' => 'decimal:2', 'target_total' => 'decimal:2', 'errors' => 'array', 'metadata' => 'array'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MigrationTemplate::class, 'template_id');
    }
}
