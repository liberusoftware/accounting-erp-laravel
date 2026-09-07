<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\XeroMigration\Enums\MigrationRecordStatus;

final class XeroMigrationRecord extends Model
{
    protected $table = 'accounting_xero_migration_records';

    protected $fillable = ['team_id', 'connection_id', 'source_type', 'source_id', 'target_type', 'target_id', 'status', 'error', 'payload'];

    protected $casts = ['status' => MigrationRecordStatus::class, 'payload' => 'array'];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(XeroConnection::class, 'connection_id');
    }
}
