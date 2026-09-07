<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MatchingIntelligence\Enums\SuggestionStatus;
use Liberu\Accounting\MatchingIntelligence\Events\SuggestionDecided;
use Liberu\Accounting\MatchingIntelligence\Exceptions\InvalidMatch;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingSuggestion;

final class DecideSuggestion
{
    public function handle(MatchingSuggestion $suggestion, string $actor, bool $accept = true, bool $automate = false): MatchingSuggestion
    {
        if ($suggestion->status !== SuggestionStatus::Suggested) {
            throw new InvalidMatch('Only suggested matches can be decided.');
        }if ($automate && $suggestion->confidence < ($suggestion->automation_threshold ?? 1)) {
            throw new InvalidMatch('Confidence is below the safe automation threshold.');
        }

        return DB::transaction(function () use ($suggestion, $actor, $accept, $automate): MatchingSuggestion {
            $result = $suggestion->update(['status' => $automate ? SuggestionStatus::Automated : ($accept ? SuggestionStatus::Accepted : SuggestionStatus::Rejected), 'metadata' => array_merge($suggestion->metadata ?? [], ['decided_by' => $actor, 'decided_at' => now()->toISOString()])]);
            $result = $suggestion->refresh();
            DB::afterCommit(fn () => event(new SuggestionDecided($result)));

            return $result;
        });
    }
}
