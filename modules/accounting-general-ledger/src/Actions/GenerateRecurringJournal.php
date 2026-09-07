<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\GeneralLedger\Exceptions\InvalidJournal;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;
use Liberu\Accounting\GeneralLedger\Models\RecurringJournal;

final class GenerateRecurringJournal
{
    public function handle(RecurringJournal $template, ?string $actor = null): JournalEntry
    {
        return DB::transaction(function () use ($template): JournalEntry {
            $template = RecurringJournal::whereKey($template->getKey())->firstOrFail();
            if (! $template->is_active || ($template->end_on && $template->next_run_on->gt($template->end_on))) {
                throw new InvalidJournal('Recurring journal is inactive or past its end date.');
            }
            $journal = app(CreateJournal::class)->handle(['book_id' => $template->book_id, 'entry_date' => $template->next_run_on->toDateString(), 'journal_type' => 'recurring', 'description' => $template->description ?: $template->name, 'source_type' => RecurringJournal::class, 'source_id' => (string) $template->id], $template->lines);
            $next = match ($template->frequency) {
                'daily' => $template->next_run_on->copy()->addDay(), 'weekly' => $template->next_run_on->copy()->addWeek(), 'monthly' => $template->next_run_on->copy()->addMonth(), 'quarterly' => $template->next_run_on->copy()->addQuarter(), 'yearly' => $template->next_run_on->copy()->addYear(),
                default => throw new InvalidJournal('Unsupported recurring journal frequency.'),
            };
            $template->update(['next_run_on' => $next, 'is_active' => $template->end_on === null || $next->lte($template->end_on)]);

            return $journal;
        });
    }
}
