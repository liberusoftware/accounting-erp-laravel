<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\OperationalReports\Enums\ReportCategory;
use Liberu\Accounting\OperationalReports\Enums\ReportRunStatus;
use Liberu\Accounting\OperationalReports\Events\ReportGenerated;
use Liberu\Accounting\OperationalReports\Exceptions\InvalidReport;
use Liberu\Accounting\OperationalReports\Models\ReportRun;

final class GenerateReport
{
    /** @param array<string,mixed> $attributes @param array<int,array<string,mixed>> $rows @param array<int,array<string,mixed>> $exceptions */
    public function handle(array $attributes, array $rows = [], array $exceptions = []): ReportRun
    {
        $category = ReportCategory::tryFrom((string) ($attributes['category'] ?? ''));
        foreach (['report_key', 'name', 'period_start', 'period_end'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidReport("Report field [{$key}] is required.");
            }
        }if (! $category) {
            throw new InvalidReport('A supported report category is required.');
        }$hash = hash('sha256', json_encode(['attributes' => $attributes, 'rows' => $rows, 'exceptions' => $exceptions], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        return DB::transaction(function () use ($attributes, $rows, $exceptions, $category, $hash): ReportRun {
            $existing = ReportRun::query()->where(['team_id' => $attributes['team_id'] ?? null, 'report_key' => $attributes['report_key'], 'source_hash' => $hash])->first();
            if ($existing) {
                return $existing->load('rows', 'exceptions');
            }$run = ReportRun::create(['team_id' => $attributes['team_id'] ?? null, 'report_key' => $attributes['report_key'], 'name' => $attributes['name'], 'category' => $category, 'period_start' => $attributes['period_start'], 'period_end' => $attributes['period_end'], 'currency' => isset($attributes['currency']) ? strtoupper((string) $attributes['currency']) : null, 'status' => ReportRunStatus::Ready, 'filters' => $attributes['filters'] ?? null, 'source_hash' => $hash, 'requested_by' => $attributes['requested_by'] ?? null, 'metadata' => $attributes['metadata'] ?? null]);
            $total = 0.0;
            foreach ($rows as $row) {
                $key = trim((string) ($row['row_key'] ?? ''));
                if ($key === '') {
                    throw new InvalidReport('Every report row requires a stable row_key.');
                }$run->rows()->create(['row_key' => $key, 'label' => $row['label'] ?? $key, 'source_type' => $row['source_type'] ?? null, 'source_id' => $row['source_id'] ?? null, 'amount' => $row['amount'] ?? 0, 'currency' => $row['currency'] ?? $run->currency, 'state' => $row['state'] ?? null, 'dimensions' => $row['dimensions'] ?? null, 'payload' => $row['payload'] ?? $row]);
                $total += (float) ($row['amount'] ?? 0);
            }foreach ($exceptions as $exception) {
                if (blank($exception['exception_key'] ?? null) || blank($exception['message'] ?? null)) {
                    throw new InvalidReport('Report exceptions require a key and message.');
                }$run->exceptions()->create(['exception_key' => $exception['exception_key'], 'severity' => $exception['severity'] ?? 'warning', 'message' => $exception['message'], 'source_type' => $exception['source_type'] ?? null, 'source_id' => $exception['source_id'] ?? null, 'status' => 'open', 'metadata' => $exception['metadata'] ?? null]);
            }$run->update(['summary' => ['row_count' => count($rows), 'exception_count' => count($exceptions), 'total_amount' => round($total, 2)]]);
            $run->audits()->create(['event_type' => 'report_generated', 'actor_id' => $attributes['requested_by'] ?? null, 'payload' => ['row_count' => count($rows), 'exception_count' => count($exceptions)], 'payload_hash' => $hash, 'created_at' => now()]);
            $run = $run->refresh()->load('rows', 'exceptions');
            DB::afterCommit(fn () => event(new ReportGenerated($run)));

            return $run;
        });
    }
}
