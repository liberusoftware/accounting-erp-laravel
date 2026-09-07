<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigration\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property string $name @property string $entity @property array<string,mixed> $mapping @property array<string,mixed>|null $metadata */
final class MigrationTemplate extends Model
{
    protected $table = 'accounting_spreadsheet_migration_templates';

    protected $fillable = ['name', 'entity', 'mapping', 'metadata'];

    protected $casts = ['mapping' => 'array', 'metadata' => 'array'];
}
