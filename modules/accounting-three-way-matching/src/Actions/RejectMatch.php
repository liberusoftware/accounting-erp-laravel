<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Actions;

use Liberu\Accounting\ThreeWayMatching\Enums\MatchStatus;
use Liberu\Accounting\ThreeWayMatching\Exceptions\InvalidMatch;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;

final class RejectMatch
{
    public function handle(MatchRecord $match, string $reason): MatchRecord
    {
        if (blank($reason) || $match->status === MatchStatus::Approved) {
            throw new InvalidMatch('Approved matches cannot be rejected and a reason is required.');
        }
        $match->update(['status' => MatchStatus::Rejected, 'rejected_reason' => $reason]);

        return $match->refresh();
    }
}
