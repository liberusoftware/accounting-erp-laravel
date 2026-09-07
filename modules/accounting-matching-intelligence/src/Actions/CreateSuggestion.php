<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MatchingIntelligence\Events\SuggestionCreated;
use Liberu\Accounting\MatchingIntelligence\Exceptions\InvalidMatch;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingSuggestion;

final class CreateSuggestion
{
    public function handle(array $attributes, array $evidence = []): MatchingSuggestion
    {
        $confidence = (float) ($attributes['confidence'] ?? -1);
        $ref = trim((string) ($attributes['suggestion_ref'] ?? ''));
        foreach (['source_type', 'source_id', 'target_type', 'target_id', 'match_type'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidMatch("Missing matching field [{$key}].");
            }
        }if ($ref === '' || $confidence < 0 || $confidence > 1) {
            throw new InvalidMatch('Suggestion reference and confidence from zero to one are required.');
        }

        return DB::transaction(function () use ($attributes, $evidence, $confidence, $ref): MatchingSuggestion {
            $existing = MatchingSuggestion::query()->where(['team_id' => $attributes['team_id'] ?? null, 'suggestion_ref' => $ref])->first();
            if ($existing) {
                return $existing->load('evidence');
            }$suggestion = MatchingSuggestion::create(['team_id' => $attributes['team_id'] ?? null, 'suggestion_ref' => $ref, 'source_type' => $attributes['source_type'], 'source_id' => $attributes['source_id'], 'target_type' => $attributes['target_type'], 'target_id' => $attributes['target_id'], 'match_type' => $attributes['match_type'], 'confidence' => $confidence, 'score' => $attributes['score'] ?? null, 'status' => 'suggested', 'automation_threshold' => $attributes['automation_threshold'] ?? null, 'explanation' => $attributes['explanation'] ?? null, 'algorithm_version' => $attributes['algorithm_version'] ?? null, 'expires_at' => $attributes['expires_at'] ?? null, 'metadata' => $attributes['metadata'] ?? null]);
            foreach ($evidence as $item) {
                if (! is_array($item) || blank($item['kind'] ?? null)) {
                    throw new InvalidMatch('Every evidence item requires a kind.');
                }$suggestion->evidence()->create(['kind' => $item['kind'], 'field' => $item['field'] ?? null, 'source_value' => $item['source_value'] ?? null, 'target_value' => $item['target_value'] ?? null, 'weight' => $item['weight'] ?? null, 'metadata' => $item['metadata'] ?? null]);
            }$result = $suggestion->load('evidence');
            DB::afterCommit(fn () => event(new SuggestionCreated($result)));

            return $result;
        });
    }
}
