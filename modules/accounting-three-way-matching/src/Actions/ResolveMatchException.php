<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ThreeWayMatching\Enums\ExceptionStatus;
use Liberu\Accounting\ThreeWayMatching\Enums\MatchStatus;
use Liberu\Accounting\ThreeWayMatching\Events\MatchExceptionResolved;
use Liberu\Accounting\ThreeWayMatching\Exceptions\InvalidMatch;
use Liberu\Accounting\ThreeWayMatching\Models\MatchException;

final class ResolveMatchException
{
    public function handle(MatchException $exception, int $actorId, string $resolution, bool $waive = false): MatchException
    {
        return DB::transaction(function () use ($exception, $actorId, $resolution, $waive): MatchException {
            if (blank($resolution) || $exception->status !== ExceptionStatus::Open) {
                throw new InvalidMatch('An open exception requires a resolution.');
            }
            $exception->update(['status' => $waive ? ExceptionStatus::Waived : ExceptionStatus::Resolved, 'resolution' => $resolution, 'resolved_by' => $actorId, 'resolved_at' => now()]);
            $exception->load('match');
            $match = $exception->match->refresh();
            if (! $match->hasBlockingExceptions() && $match->status === MatchStatus::Exception) {
                $match->update(['status' => ($match->metadata['base_status'] ?? MatchStatus::Matched->value)]);
            }
            $exception = $exception->refresh();
            DB::afterCommit(fn () => event(new MatchExceptionResolved($exception)));

            return $exception;
        });
    }
}
