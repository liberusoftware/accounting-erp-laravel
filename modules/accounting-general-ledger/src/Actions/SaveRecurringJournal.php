<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\GeneralLedger\Exceptions\InvalidJournal;
use Liberu\Accounting\GeneralLedger\Models\RecurringJournal;

final class SaveRecurringJournal
{
    public function handle(array $attributes, ?RecurringJournal $template = null): RecurringJournal
    {
        $allowed = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];
        if (! in_array($attributes['frequency'] ?? '', $allowed, true)) {
            throw new InvalidJournal('Unsupported recurring journal frequency.');
        }if (empty($attributes['lines']) || count($attributes['lines']) < 2) {
            throw new InvalidJournal('A recurring journal requires at least two lines.');
        }

        return DB::transaction(function () use ($attributes, $template) {
            $template ??= new RecurringJournal();
            $template->fill($attributes);
            $template->save();

            return $template->refresh();
        });
    }
}
