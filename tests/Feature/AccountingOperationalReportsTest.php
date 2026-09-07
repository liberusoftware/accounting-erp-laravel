<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\OperationalReports\Actions\GenerateReport;
use Liberu\Accounting\OperationalReports\Actions\PublishReport;
use Liberu\Accounting\OperationalReports\Enums\ReportCategory;
use Liberu\Accounting\OperationalReports\Enums\ReportRunStatus;
use Liberu\Accounting\OperationalReports\Exceptions\InvalidReport;
use Liberu\Accounting\OperationalReports\Queries\ReportQuery;

uses(RefreshDatabase::class);

function operationalReportAttributes(): array
{
    return ['team_id' => 1, 'report_key' => 'ar-aging', 'name' => 'Receivables aging', 'category' => 'receivables_payables', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'currency' => 'GBP', 'filters' => ['as_of' => '2026-01-31']];
}

it('generates idempotent report snapshots across the report families', function (): void {
    $rows = [['row_key' => 'customer-1', 'label' => 'Customer 1', 'amount' => 125, 'currency' => 'GBP', 'state' => 'overdue', 'dimensions' => ['bucket' => '31-60']]];
    $exceptions = [['exception_key' => 'missing-invoice', 'severity' => 'warning', 'message' => 'Invoice source was unavailable.']];
    $run = app(GenerateReport::class)->handle(operationalReportAttributes(), $rows, $exceptions);
    $same = app(GenerateReport::class)->handle(operationalReportAttributes(), $rows, $exceptions);

    expect($same->id)->toBe($run->id)
        ->and($run->category)->toBe(ReportCategory::ReceivablesPayables)
        ->and($run->status)->toBe(ReportRunStatus::Ready)
        ->and($run->summary['total_amount'])->toEqual(125.0)
        ->and($run->rows)->toHaveCount(1)
        ->and($run->exceptions)->toHaveCount(1);
});

it('requires blocking exceptions to be resolved before publication', function (): void {
    $run = app(GenerateReport::class)->handle(array_merge(operationalReportAttributes(), ['report_key' => 'tax-exceptions', 'category' => 'tax']), [], [['exception_key' => 'tax-drift', 'severity' => 'blocking', 'message' => 'Tax source differs.']]);
    expect(fn (): mixed => app(PublishReport::class)->handle($run, 7))->toThrow(InvalidReport::class);
    $run->exceptions()->update(['status' => 'resolved', 'resolution' => 'Reviewed against source.', 'resolved_by' => 7, 'resolved_at' => now()]);
    expect(app(PublishReport::class)->handle($run->refresh(), 7)->status)->toBe(ReportRunStatus::Published);
});

it('is tenant scoped and exposes the operational reports API', function (): void {
    app(GenerateReport::class)->handle(operationalReportAttributes(), []);
    expect(app(ReportQuery::class)->paginate(1)->total())->toBe(1)->and(app(ReportQuery::class)->paginate(2)->total())->toBe(0);
    Sanctum::actingAs(User::factory()->create(), ['accounting.operational-reports.read', 'accounting.operational-reports.write']);
    $this->getJson('/api/v1/accounting/operational-reports')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/operational-reports/exceptions')->assertOk();
});
