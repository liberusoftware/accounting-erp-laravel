<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionRunStatus;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionStatus;
use Liberu\Accounting\RevenueRecognition\Models\RevenueRecognitionRun;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;
use Liberu\Accounting\RevenueRecognition\Models\RevenueScheduleEntry;

final class RecognizeDueRevenue
{
    public function handle(RevenueSchedule $schedule, string $asOfDate, ?int $teamId = null): RevenueRecognitionRun
    {
        $run = RevenueRecognitionRun::create(['team_id' => $teamId, 'as_of_date' => $asOfDate, 'status' => RecognitionRunStatus::Running, 'started_at' => now()]);
        if (! $schedule->funded || $schedule->status !== RecognitionStatus::Active) {
            $run->update(['status' => RecognitionRunStatus::Completed, 'finished_at' => now(), 'metadata' => ['skipped' => 'schedule is not funded or active']]);

            return $run;
        }try {
            $count = 0;
            $schedule->entries()->where('status', RecognitionStatus::Active)->whereDate('recognition_date', '<=', $asOfDate)->orderBy('period_number')->each(function (RevenueScheduleEntry $entry) use (&$count): void {
                DB::transaction(function () use ($entry): void {
                    $entry->update(['status' => RecognitionStatus::Completed, 'recognized_at' => now(), 'ledger_reference' => 'revenue-recognition:'.$entry->id]);
                });
                $count++;
            });
            $remaining = $schedule->entries()->where('status', RecognitionStatus::Active)->count();
            $schedule->update(['status' => $remaining === 0 ? RecognitionStatus::Completed : RecognitionStatus::Active]);
            $run->update(['status' => RecognitionRunStatus::Completed, 'processed_entries' => $count, 'finished_at' => now()]);

            return $run->refresh();
        } catch (\Throwable $exception) {
            $run->update(['status' => RecognitionRunStatus::Failed, 'failed_entries' => 1, 'errors' => ['message' => $exception->getMessage()], 'finished_at' => now()]);
            throw $exception;
        }
    }
}
