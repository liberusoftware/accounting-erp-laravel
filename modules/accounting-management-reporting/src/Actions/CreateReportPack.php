<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ManagementReporting\Enums\ReportStatus;
use Liberu\Accounting\ManagementReporting\Exceptions\InvalidReport;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;

final class CreateReportPack
{
    public function handle(array $attributes): ReportPack
    {
        $ref = trim((string) ($attributes['report_ref'] ?? ''));
        if ($ref === '' || blank($attributes['name'] ?? null) || blank($attributes['period_start'] ?? null) || blank($attributes['period_end'] ?? null) || blank($attributes['currency'] ?? null)) {
            throw new InvalidReport('A report pack requires reference, name, period, and currency.');
        }if ($attributes['period_end'] < $attributes['period_start']) {
            throw new InvalidReport('Report period end must not precede its start.');
        }

        return DB::transaction(fn (): ReportPack => ReportPack::create(['team_id' => $attributes['team_id'] ?? null, 'report_ref' => $ref, 'name' => $attributes['name'], 'period_start' => $attributes['period_start'], 'period_end' => $attributes['period_end'], 'currency' => strtoupper($attributes['currency']), 'status' => ReportStatus::Draft, 'version' => $attributes['version'] ?? 1, 'owner_ref' => $attributes['owner_ref'] ?? null, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
