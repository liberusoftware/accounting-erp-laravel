<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitability\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ProjectProfitability\Enums\ProfitabilityStatus;
use Liberu\Accounting\ProjectProfitability\Exceptions\InvalidProfitability;
use Liberu\Accounting\ProjectProfitability\Models\ProjectProfitability;

final class FinalizeProjectProfitability
{
    public function handle(ProjectProfitability $record): ProjectProfitability
    {
        if ($record->status === ProfitabilityStatus::Reversed) {
            throw new InvalidProfitability('A reversed profitability record cannot be finalized.');
        }

        return DB::transaction(function () use ($record): ProjectProfitability {
            $record->update(['status' => ProfitabilityStatus::Final]);

            return $record->refresh();
        });
    }
}
