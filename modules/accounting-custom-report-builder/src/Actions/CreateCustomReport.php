<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder\Actions;

use Liberu\Accounting\CustomReportBuilder\Exceptions\InvalidCustomReport;
use Liberu\Accounting\CustomReportBuilder\Models\CustomReport;

final class CreateCustomReport
{
    public function handle(array $attributes): CustomReport
    {
        foreach (['team_id', 'report_ref', 'name', 'measures'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCustomReport("{$field} is required.");
            }
        } if (! is_array($attributes['measures']) || count($attributes['measures']) === 0) {
            throw new InvalidCustomReport('At least one governed measure is required.');
        }

        return CustomReport::create($attributes);
    }
}
