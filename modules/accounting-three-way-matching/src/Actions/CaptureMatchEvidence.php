<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ThreeWayMatching\Models\MatchEvidence;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;

final class CaptureMatchEvidence
{
    public function handle(MatchRecord $match, string $sourceType, string $sourceId, array $snapshot, ?int $actorId = null): MatchEvidence
    {
        $json = json_encode($snapshot, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        return DB::transaction(function () use ($match, $sourceType, $sourceId, $snapshot, $actorId, $json): MatchEvidence {
            /** @var MatchEvidence $evidence */
            $evidence = $match->evidence()->firstOrCreate(['source_type' => $sourceType, 'source_id' => $sourceId, 'snapshot_hash' => hash('sha256', $json)], ['snapshot' => $snapshot, 'captured_by' => $actorId]);

            return $evidence;
        });
    }
}
