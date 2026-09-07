<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int|null $team_id @property string $provider @property string|null $external_business_id @property string $status */
final class SageMigrationConnection extends Model
{
    protected $table = 'accounting_sage_migration_connections';

    protected $fillable = ['team_id', 'provider', 'external_business_id', 'access_token', 'refresh_token', 'token_expires_at', 'status', 'last_synced_at'];

    protected $hidden = ['access_token', 'refresh_token'];

    protected $casts = ['access_token' => 'encrypted', 'refresh_token' => 'encrypted', 'token_expires_at' => 'datetime', 'last_synced_at' => 'datetime'];
}
