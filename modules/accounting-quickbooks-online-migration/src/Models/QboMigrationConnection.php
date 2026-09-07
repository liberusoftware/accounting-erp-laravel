<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $realm_id
 * @property string $status
 * @property Carbon|null $last_synced_at
 */
final class QboMigrationConnection extends Model
{
    protected $table = 'accounting_qbo_migration_connections';

    protected $fillable = ['team_id', 'realm_id', 'access_token', 'refresh_token', 'token_expires_at', 'status', 'last_synced_at'];

    protected $hidden = ['access_token', 'refresh_token'];

    protected $casts = ['access_token' => 'encrypted', 'refresh_token' => 'encrypted', 'token_expires_at' => 'datetime', 'last_synced_at' => 'datetime'];
}
