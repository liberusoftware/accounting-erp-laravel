<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SpreadsheetMigration\Enums\MigrationStatus;
use Liberu\Accounting\SpreadsheetMigration\Exceptions\InvalidMigration;
use Liberu\Accounting\SpreadsheetMigration\Models\MigrationRun;

final class ValidateMigration
{
    public function handle(MigrationRun $run): MigrationRun
    {
        return DB::transaction(function () use ($run): MigrationRun {
            if ($run->row_count < 1) {
                throw new InvalidMigration('A migration must contain at least one row.');
            }if (abs((float) $run->source_total - (float) $run->target_total) > 0.01) {
                $run->update(['status' => MigrationStatus::Failed, 'errors' => ['balance' => 'Source and target totals do not balance.']]);
                throw new InvalidMigration('Migration totals do not balance.');
            }$run->update(['status' => MigrationStatus::Validated, 'errors' => null]);

            return $run->refresh();
        });
    }
}
