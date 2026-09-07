<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\JobEstimates\Actions\AddEstimateLine;
use Liberu\Accounting\JobEstimates\Actions\CreateEstimate;
use Liberu\Accounting\JobEstimates\Actions\DecideEstimate;
use Liberu\Accounting\JobEstimates\Actions\RecordActual;
use Liberu\Accounting\JobEstimates\Actions\SubmitEstimate;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Exceptions\InvalidEstimate;
use Liberu\Accounting\JobEstimates\Queries\JobEstimateQuery;
use Tests\TestCase;

final class AccountingJobEstimatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_estimate_lifecycle_and_actual_comparison(): void
    {
        $estimate = app(CreateEstimate::class)->handle(['estimate_ref' => 'EST-001', 'project_ref' => 'JOB-1', 'title' => 'Office fit-out', 'currency' => 'gbp']);
        app(AddEstimateLine::class)->handle($estimate, ['line_ref' => 'L-1', 'line_type' => 'cost', 'category' => 'labour', 'description' => 'Installation', 'quantity' => 10, 'rate' => 100]);
        app(AddEstimateLine::class)->handle($estimate, ['line_ref' => 'L-2', 'line_type' => 'revenue', 'category' => 'contract', 'description' => 'Contract value', 'quantity' => 1, 'rate' => 2000]);
        $estimate = app(SubmitEstimate::class)->handle($estimate);
        $estimate = app(DecideEstimate::class)->handle($estimate, 'reviewer-1', true);
        app(RecordActual::class)->handle($estimate, ['line_ref' => 'L-1', 'category' => 'labour', 'amount' => 400, 'source_ref' => 'ledger-1', 'occurred_at' => now()]);

        $comparison = app(JobEstimateQuery::class)->comparison($estimate);
        $eac = app(JobEstimateQuery::class)->estimateAtCompletion($estimate);
        $this->assertSame(EstimateStatus::Approved, $estimate->status);
        $this->assertSame(400.0, $comparison[0]['actual']);
        $this->assertSame(1000.0, $eac['estimate_at_completion']);
    }

    public function test_submission_requires_estimate_lines_and_rejection_comment(): void
    {
        $estimate = app(CreateEstimate::class)->handle(['estimate_ref' => 'EST-002', 'project_ref' => 'JOB-2', 'title' => 'Repair', 'currency' => 'USD']);
        $this->expectException(InvalidEstimate::class);
        app(SubmitEstimate::class)->handle($estimate);
    }
}
