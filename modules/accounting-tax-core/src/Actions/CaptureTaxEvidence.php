<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\TaxCore\Events\TaxEvidenceCaptured;
use Liberu\Accounting\TaxCore\Models\TaxEvidence;
use Liberu\Accounting\TaxCore\Models\TaxRule;

final class CaptureTaxEvidence
{
    public function handle(TaxRule $rule, string $sourceType, string $sourceId, array $snapshot, ?int $actorId = null): TaxEvidence
    {
        $encoded = json_encode($snapshot, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $hash = hash('sha256', $encoded);

        return DB::transaction(function () use ($rule, $sourceType, $sourceId, $snapshot, $hash, $actorId): TaxEvidence {
            $evidence = TaxEvidence::query()->firstOrCreate(['tax_rule_id' => $rule->id, 'source_type' => $sourceType, 'source_id' => $sourceId, 'snapshot_hash' => $hash], ['snapshot' => $snapshot, 'captured_by' => $actorId]);
            DB::afterCommit(fn () => event(new TaxEvidenceCaptured($evidence)));

            return $evidence;
        });
    }
}
