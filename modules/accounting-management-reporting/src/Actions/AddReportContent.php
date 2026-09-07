<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ManagementReporting\Exceptions\InvalidReport;
use Liberu\Accounting\ManagementReporting\Models\ReportChart;
use Liberu\Accounting\ManagementReporting\Models\ReportNarrative;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;

final class AddReportContent
{
    public function narrative(ReportPack $report, array $attributes): ReportNarrative
    {
        $section = trim((string) ($attributes['section_ref'] ?? ''));
        if ($section === '' || blank($attributes['title'] ?? null) || blank($attributes['body'] ?? null)) {
            throw new InvalidReport('Narratives require section, title, and body.');
        }

        return DB::transaction(fn (): ReportNarrative => ReportNarrative::create(['report_pack_id' => $report->id, 'section_ref' => $section, 'title' => $attributes['title'], 'body' => $attributes['body'], 'author_ref' => $attributes['author_ref'] ?? null, 'version' => $attributes['version'] ?? 1, 'metadata' => $attributes['metadata'] ?? null]));
    }

    public function chart(ReportPack $report, array $attributes): ReportChart
    {
        if (blank($attributes['chart_ref'] ?? null) || blank($attributes['title'] ?? null) || blank($attributes['chart_type'] ?? null) || ! is_array($attributes['series'] ?? null)) {
            throw new InvalidReport('Charts require reference, title, type, and series.');
        }

        return DB::transaction(fn (): ReportChart => ReportChart::create(['report_pack_id' => $report->id, 'chart_ref' => $attributes['chart_ref'], 'title' => $attributes['title'], 'chart_type' => $attributes['chart_type'], 'data_source' => $attributes['data_source'] ?? 'module-contract', 'series' => $attributes['series'], 'options' => $attributes['options'] ?? null, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
