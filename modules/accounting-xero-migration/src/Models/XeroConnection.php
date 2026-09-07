<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\XeroMigration\Enums\XeroConnectionStatus;

final class XeroConnection extends Model
{
    protected $table = 'accounting_xero_connections';

    protected $fillable = ['team_id', 'tenant_ref', 'access_token', 'refresh_token', 'token_expires_at', 'status', 'last_synced_at', 'metadata'];

    protected $hidden = ['access_token', 'refresh_token'];

    protected $casts = ['access_token' => 'encrypted', 'refresh_token' => 'encrypted', 'token_expires_at' => 'datetime', 'last_synced_at' => 'datetime', 'status' => XeroConnectionStatus::class, 'metadata' => 'array'];

    public function migrationRecords(): HasMany
    {
        return $this->hasMany(XeroMigrationRecord::class, 'connection_id');
    }
}
