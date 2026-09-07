<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RecurringTransactions\Enums\OccurrenceStatus;
use Liberu\Accounting\RecurringTransactions\Enums\RecurringStatus;
use Liberu\Accounting\RecurringTransactions\Models\RecurringException;
use Liberu\Accounting\RecurringTransactions\Models\RecurringOccurrence;
use Liberu\Accounting\RecurringTransactions\Models\RecurringTemplate;

final class GenerateRecurringOccurrences
{
    public function handle(RecurringTemplate $template, string $throughDate, ?int $safetyCap = 120): int
    {
        $template->refresh();
        if ($template->status !== RecurringStatus::Active) {
            return 0;
        }$cursor = CarbonImmutable::parse($template->next_run_on);
        $through = CarbonImmutable::parse($throughDate);
        $count = 0;
        while ($cursor->lte($through) && ($template->ends_on === null || $cursor->lte($template->ends_on)) && $count < $safetyCap) {
            $key = $template->id.':'.$cursor->toDateString();
            try {
                DB::transaction(function () use ($template, $cursor, $key): void {
                    RecurringOccurrence::firstOrCreate(['template_id' => $template->id, 'idempotency_key' => $key], ['occurrence_on' => $cursor->toDateString(), 'status' => $template->automatic ? OccurrenceStatus::Generated : OccurrenceStatus::Draft, 'generated_payload' => $template->payload, 'generated_at' => $template->automatic ? now() : null]);
                    $template->update(['next_run_on' => $this->next($template->frequency, $cursor)]);
                });
                $count++;
            } catch (\Throwable $exception) {
                RecurringException::create(['template_id' => $template->id, 'kind' => 'generation', 'message' => $exception->getMessage(), 'status' => 'open']);
                $template->update(['status' => RecurringStatus::Failed]);
                break;
            }$cursor = CarbonImmutable::parse($template->fresh()->next_run_on);
        }if ($template->ends_on !== null && $cursor->gt($template->ends_on)) {
            $template->update(['status' => RecurringStatus::Expired]);
        }

        return $count;
    }

    private function next(string $frequency, CarbonImmutable $date): string
    {
        return match ($frequency) {
            'daily' => $date->addDay()->toDateString(),'weekly' => $date->addWeek()->toDateString(),'monthly' => $date->addMonth()->toDateString(),'quarterly' => $date->addMonths(3)->toDateString(),'yearly' => $date->addYear()->toDateString(),default => $date->toDateString()
        };
    }
}
