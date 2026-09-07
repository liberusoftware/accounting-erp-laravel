<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Depreciation\Enums\DepreciationRunStatus;
use Liberu\Accounting\Depreciation\Exceptions\InvalidDepreciation;
use Liberu\Accounting\Depreciation\Models\DepreciationRun;

final class PostDepreciationRun
{
    public function handle(DepreciationRun $run, int $actorId, string $journalReference): DepreciationRun
    {
        if ($run->status !== DepreciationRunStatus::Calculated || blank($journalReference)) {
            throw new InvalidDepreciation('Only calculated runs with a journal reference can be posted.');
        }

        return DB::transaction(function () use ($run, $actorId, $journalReference): DepreciationRun {
            $run->update(['status' => DepreciationRunStatus::Posted, 'posted_by' => $actorId, 'posted_at' => now(), 'journal_ref' => $journalReference]);

            return $run->fresh('schedule');
        });
    }
}
