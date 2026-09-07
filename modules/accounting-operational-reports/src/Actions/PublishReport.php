<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\OperationalReports\Enums\ReportRunStatus;
use Liberu\Accounting\OperationalReports\Events\ReportPublished;
use Liberu\Accounting\OperationalReports\Exceptions\InvalidReport;
use Liberu\Accounting\OperationalReports\Models\ReportRun;

final class PublishReport
{
    public function handle(ReportRun $run, int $actorId): ReportRun
    {
        return DB::transaction(function () use ($run, $actorId): ReportRun {
            $run = ReportRun::query()->lockForUpdate()->findOrFail($run->id);
            if (! in_array($run->status, [ReportRunStatus::Ready, ReportRunStatus::Published], true)) {
                throw new InvalidReport('Only a ready report can be published.');
            }if ($run->exceptions()->where('severity', 'blocking')->where('status', 'open')->exists()) {
                throw new InvalidReport('Blocking report exceptions must be resolved before publication.');
            }$run->update(['status' => ReportRunStatus::Published, 'published_by' => $actorId, 'published_at' => now()]);
            $run->audits()->create(['event_type' => 'report_published', 'actor_id' => $actorId, 'payload' => ['run_id' => $run->id], 'payload_hash' => hash('sha256', (string) $run->id.':published'), 'created_at' => now()]);
            $result = $run->refresh();
            DB::afterCommit(fn () => event(new ReportPublished($result)));

            return $result;
        });
    }
}
