<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionRunStatus;
use Liberu\Accounting\RevenueRecognition\Models\RevenueRecognitionRun;
use Liberu\Accounting\RevenueRecognition\Models\RevenueReconciliation;

final class ReconcileRevenueRun
{
    public function handle(RevenueRecognitionRun $run, array $references = []): RevenueRecognitionRun
    {
        return DB::transaction(function () use ($run, $references): RevenueRecognitionRun {
            $variance = false;
            foreach ($references as $reference) {
                $expected = round((float) ($reference['expected_amount'] ?? 0), 2);
                $recognized = round((float) ($reference['recognized_amount'] ?? 0), 2);
                $delta = round($expected - $recognized, 2);
                $variance = $variance || abs($delta) > 0.01;
                RevenueReconciliation::create(['run_id' => $run->id, 'reference_type' => $reference['reference_type'] ?? 'schedule', 'reference_id' => (string) $reference['reference_id'], 'expected_amount' => $expected, 'recognized_amount' => $recognized, 'variance' => $delta, 'status' => abs($delta) > 0.01 ? 'exception' : 'matched', 'notes' => $reference['notes'] ?? null]);
            }$run->update(['status' => $variance ? RecognitionRunStatus::Failed : RecognitionRunStatus::Reconciled, 'metadata' => array_merge($run->metadata ?? [], ['reconciled_at' => now()->toIso8601String()])]);

            return $run->refresh();
        });
    }
}
