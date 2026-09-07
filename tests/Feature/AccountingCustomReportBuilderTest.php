<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CustomReportBuilder\Actions\CreateCustomReport;
use Liberu\Accounting\CustomReportBuilder\Actions\RequestReportExport;
use Liberu\Accounting\CustomReportBuilder\Actions\SaveReportVariant;
use Liberu\Accounting\CustomReportBuilder\Enums\ReportExportStatus;
use Liberu\Accounting\CustomReportBuilder\Exceptions\InvalidCustomReport;

uses(RefreshDatabase::class);

it('creates a governed report with a saved variant and export request', function (): void {
    $report = app(CreateCustomReport::class)->handle(['team_id' => 71, 'report_ref' => 'P-L', 'name' => 'Profit and loss', 'measures' => ['revenue', 'cost'], 'dimensions' => ['account'], 'filters' => ['period' => '2026-Q3'], 'grouping' => ['account'], 'formulas' => ['gross_profit' => 'revenue-cost'], 'comparisons' => ['prior_period'], 'layout' => ['table'], 'permissions' => ['roles' => ['finance']]]);
    $variant = app(SaveReportVariant::class)->handle($report, 'board', ['layout' => 'chart']);
    $export = app(RequestReportExport::class)->handle($report, 'csv', ['period' => '2026-Q3']);
    expect($variant->variant_ref)->toBe('board')->and($export->status)->toBe(ReportExportStatus::Requested);
});

it('requires at least one governed measure', function (): void {
    expect(fn () => app(CreateCustomReport::class)->handle(['team_id' => 71, 'report_ref' => 'EMPTY', 'name' => 'Empty', 'measures' => []]))->toThrow(InvalidCustomReport::class);
});
