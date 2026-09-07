<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ThreeWayMatching\Enums\MatchStatus;
use Liberu\Accounting\ThreeWayMatching\Events\MatchApproved;
use Liberu\Accounting\ThreeWayMatching\Exceptions\InvalidMatch;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;

final class ApproveMatch
{
    public function handle(MatchRecord $match, int $actorId, ?string $overrideReason = null): MatchRecord
    {
        return DB::transaction(function () use ($match, $actorId, $overrideReason): MatchRecord {
            $match = MatchRecord::query()->lockForUpdate()->findOrFail($match->id);
            if (! in_array($match->status, [MatchStatus::Matched, MatchStatus::Partial, MatchStatus::Exception], true) || ($match->hasBlockingExceptions() && blank($overrideReason))) {
                throw new InvalidMatch('This match cannot be approved without resolving blocking exceptions or recording an override reason.');
            }
            $metadata = $match->metadata ?? [];
            if ($overrideReason) {
                $metadata['approval_override_reason'] = $overrideReason;
            }
            $match->update(['status' => MatchStatus::Approved, 'approved_by' => $actorId, 'approved_at' => now(), 'metadata' => $metadata]);
            $match = $match->refresh()->load('exceptions', 'evidence');
            DB::afterCommit(fn () => event(new MatchApproved($match)));

            return $match;
        });
    }
}
