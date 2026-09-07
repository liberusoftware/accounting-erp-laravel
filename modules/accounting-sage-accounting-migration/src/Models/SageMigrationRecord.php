<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\SageAccountingMigration\Enums\MigrationRecordStatus;

/**
 * @property int $id
 * @property int $run_id
 * @property string $entity_type
 * @property string $source_id
 * @property MigrationRecordStatus $status
 * @property array<string,mixed> $payload
 * @property string|null $payload_hash
 * @property string|null $error_message
 * @property array<string,mixed>|null $metadata
 */
final class SageMigrationRecord extends Model
{
    protected $table = 'accounting_sage_migration_records';

    protected $fillable = ['run_id', 'entity_type', 'source_id', 'status', 'payload', 'payload_hash', 'error_message', 'metadata'];

    protected $casts = ['status' => MigrationRecordStatus::class, 'payload' => 'array', 'metadata' => 'array'];
}
