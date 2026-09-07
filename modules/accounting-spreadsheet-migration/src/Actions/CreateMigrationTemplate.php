<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SpreadsheetMigration\Exceptions\InvalidMigration;
use Liberu\Accounting\SpreadsheetMigration\Models\MigrationTemplate;

final class CreateMigrationTemplate
{
    public function handle(array $attributes): MigrationTemplate
    {
        return DB::transaction(function () use ($attributes): MigrationTemplate {
            foreach (['name', 'entity', 'mapping'] as $key) {
                if (blank($attributes[$key] ?? null)) {
                    throw new InvalidMigration("Template field [{$key}] is required.");
                }
            }if (! is_array($attributes['mapping'])) {
                throw new InvalidMigration('Template mapping must be an array.');
            }

            return MigrationTemplate::create($attributes);
        });
    }
}
