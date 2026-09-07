<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder\Actions;

use Liberu\Accounting\CustomReportBuilder\Models\CustomReport;
use Liberu\Accounting\CustomReportBuilder\Models\CustomReportVariant;

final class SaveReportVariant
{
    public function handle(CustomReport $report, string $variantRef, array $configuration): CustomReportVariant
    {
        return $report->variants()->updateOrCreate(['variant_ref' => $variantRef], ['team_id' => $report->team_id, 'configuration' => $configuration]);
    }
}
