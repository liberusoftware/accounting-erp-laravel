<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;

final class ReportQuery
{
    public function packs(?int $teamId = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        $q = ReportPack::query()->withCount(['narratives', 'charts', 'reviews', 'deliveries'])->latest('period_end');
        if ($teamId !== null) {
            $q->where('team_id', $teamId);
        }if ($status !== null) {
            $q->where('status', $status);
        }

        return $q->paginate(min(max($perPage, 1), 100));
    }

    public function summary(ReportPack $report): array
    {
        return ['narratives' => $report->narratives()->count(), 'charts' => $report->charts()->count(), 'reviews' => $report->reviews()->count(), 'deliveries' => $report->deliveries()->count(), 'status' => $report->status->value];
    }
}
