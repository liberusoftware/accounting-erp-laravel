<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\YearEnd\Enums\YearEndStatus;
use Liberu\Accounting\YearEnd\Exceptions\InvalidYearEnd;
use Liberu\Accounting\YearEnd\Models\YearEndClose;

final class CreateYearEndClose
{
    public function handle(array $attributes): YearEndClose
    {
        if ((int) ($attributes['fiscal_year'] ?? 0) < 2000 || blank($attributes['period_end'] ?? null) || blank($attributes['retained_earnings_account_ref'] ?? null)) {
            throw new InvalidYearEnd('A fiscal year, period end, and retained earnings account are required.');
        }

        return DB::transaction(fn (): YearEndClose => YearEndClose::create(array_merge($attributes, ['status' => $attributes['status'] ?? YearEndStatus::Open])));
    }
}
