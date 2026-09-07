<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacks\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RegionalPacks\Enums\RegionalArtifactType;
use Liberu\Accounting\RegionalPacks\Models\RegionalPack;
use Liberu\Accounting\RegionalPacks\Models\RegionalPackArtifact;

final class RunComplianceTests
{
    public function handle(RegionalPack $pack): RegionalPack
    {
        return DB::transaction(function () use ($pack): RegionalPack {
            $pack->artifacts()->where('type', RegionalArtifactType::ComplianceTest)->each(function (RegionalPackArtifact $artifact): void {
                $definition = $artifact->definition;
                $passed = ($definition['expected'] ?? null) == ($definition['actual'] ?? null);
                $artifact->update(['status' => $passed ? 'passed' : 'failed', 'test_results' => ['passed' => $passed, 'executed_at' => now()->toIso8601String()]]);
            });

            return $pack->refresh();
        });
    }
}
