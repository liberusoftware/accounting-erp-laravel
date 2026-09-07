<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SpreadsheetMigration\Enums\MigrationMode;
use Liberu\Accounting\SpreadsheetMigration\Models\MigrationRun;
use Liberu\Accounting\SpreadsheetMigration\Models\MigrationTemplate;

final class CreateMigrationRun
{
    public function handle(MigrationTemplate $template, array $attributes): MigrationRun
    {
        $rows = $attributes['rows'] ?? [];
        $encoded = json_encode($rows, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        return DB::transaction(fn (): MigrationRun => MigrationRun::firstOrCreate(['template_id' => $template->id, 'source_hash' => hash('sha256', $encoded)], ['mode' => $attributes['mode'] ?? MigrationMode::Detail, 'row_count' => count($rows), 'source_total' => $attributes['source_total'] ?? 0, 'target_total' => $attributes['target_total'] ?? 0, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
