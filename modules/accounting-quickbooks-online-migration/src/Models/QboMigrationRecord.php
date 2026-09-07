<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\QuickBooksOnlineMigration\Enums\RecordStatus;

/**
 * @property int $id
 * @property int $run_id
 * @property string $entity_type
 * @property string $source_id
 * @property RecordStatus $status
 * @property array<string,mixed> $payload
 * @property string|null $payload_hash
 */
final class QboMigrationRecord extends Model
{
    protected $table = 'accounting_qbo_migration_records';

    protected $fillable = ['run_id', 'entity_type', 'source_id', 'status', 'payload', 'payload_hash', 'error_message', 'metadata'];

    protected $casts = ['status' => RecordStatus::class, 'payload' => 'array', 'metadata' => 'array'];
}
