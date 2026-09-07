<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MatchingIntelligence\Enums\FeedbackType;
use Liberu\Accounting\MatchingIntelligence\Exceptions\InvalidMatch;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingFeedback;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingSuggestion;

final class RecordFeedback
{
    public function handle(MatchingSuggestion $suggestion, string $actor, string $type, ?string $comment = null): MatchingFeedback
    {
        try {
            $feedback = FeedbackType::from($type);
        } catch (\ValueError) {
            throw new InvalidMatch('Feedback must be correct, incorrect, or partial.');
        }

        return DB::transaction(fn (): MatchingFeedback => MatchingFeedback::create(['suggestion_id' => $suggestion->id, 'actor_ref' => $actor, 'feedback_type' => $feedback, 'comment' => $comment]));
    }
}
