<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ManagementReporting\Actions\AddReportContent;
use Liberu\Accounting\ManagementReporting\Actions\ArchiveReport;
use Liberu\Accounting\ManagementReporting\Actions\CreateReportPack;
use Liberu\Accounting\ManagementReporting\Actions\DeliverReport;
use Liberu\Accounting\ManagementReporting\Actions\ReviewReport;
use Liberu\Accounting\ManagementReporting\Enums\ReportStatus;
use Liberu\Accounting\ManagementReporting\Exceptions\InvalidReport;
use Liberu\Accounting\ManagementReporting\Queries\ReportQuery;

uses(RefreshDatabase::class);

it('builds, reviews, delivers, and archives a management report pack', function (): void {
    $report = app(CreateReportPack::class)->handle(['team_id' => 1, 'report_ref' => 'MR-2026-Q1', 'name' => 'Quarterly management pack', 'period_start' => '2026-01-01', 'period_end' => '2026-03-31', 'currency' => 'GBP']);
    app(AddReportContent::class)->narrative($report, ['section_ref' => 'overview', 'title' => 'Overview', 'body' => 'Trading remained strong.']);
    app(AddReportContent::class)->chart($report, ['chart_ref' => 'revenue', 'title' => 'Revenue', 'chart_type' => 'line', 'series' => [['label' => 'Revenue', 'data' => [10, 20]]]]);
    $report = app(ReviewReport::class)->handle($report, 'cfo-1', 'approved');
    $delivery = app(DeliverReport::class)->handle($report, 'pdf', ['file_ref' => 'reports/MR-2026-Q1.pdf', 'recipients' => ['board@example.test']]);
    $archived = app(ArchiveReport::class)->handle($report);
    expect($delivery->status)->toBe('delivered')->and($archived->status)->toBe(ReportStatus::Archived)->and(app(ReportQuery::class)->summary($archived)['narratives'])->toBe(1);
});

it('rejects invalid periods and delivery before approval', function (): void {
    expect(fn (): mixed => app(CreateReportPack::class)->handle(['report_ref' => 'BAD', 'name' => 'Bad', 'period_start' => '2026-03-01', 'period_end' => '2026-01-01', 'currency' => 'GBP']))->toThrow(InvalidReport::class);
    $report = app(CreateReportPack::class)->handle(['report_ref' => 'MR-1', 'name' => 'Report', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'currency' => 'GBP']);
    expect(fn (): mixed => app(DeliverReport::class)->handle($report, 'spreadsheet'))->toThrow(InvalidReport::class);
});

it('exposes authenticated management reporting API routes', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.management-reporting.read', 'accounting.management-reporting.write']);
    $this->postJson('/api/v1/accounting/management-reporting/packs', ['team_id' => 1, 'report_ref' => 'API-MR-1', 'name' => 'API report', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'currency' => 'GBP'])->assertCreated()->assertJsonPath('data.type', 'accounting-management-report-pack');
    $this->getJson('/api/v1/accounting/management-reporting/packs')->assertOk();
});
