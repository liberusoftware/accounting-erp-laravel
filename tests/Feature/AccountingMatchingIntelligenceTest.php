<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\MatchingIntelligence\Actions\ConfigureThreshold;
use Liberu\Accounting\MatchingIntelligence\Actions\CreateSuggestion;
use Liberu\Accounting\MatchingIntelligence\Actions\DecideSuggestion;
use Liberu\Accounting\MatchingIntelligence\Actions\RecordFeedback;
use Liberu\Accounting\MatchingIntelligence\Enums\SuggestionStatus;
use Liberu\Accounting\MatchingIntelligence\Exceptions\InvalidMatch;
use Liberu\Accounting\MatchingIntelligence\Queries\MatchingQuery;

uses(RefreshDatabase::class);

it('creates explainable suggestions, enforces automation confidence, and records feedback', function (): void {
    $threshold = app(ConfigureThreshold::class)->handle(['team_id' => 1, 'match_type' => 'bank-payment', 'minimum_confidence' => .9]);
    $suggestion = app(CreateSuggestion::class)->handle(['team_id' => 1, 'suggestion_ref' => 'MATCH-1', 'source_type' => 'bank', 'source_id' => 'bank-1', 'target_type' => 'payment', 'target_id' => 'payment-1', 'match_type' => 'bank-payment', 'confidence' => .95, 'automation_threshold' => $threshold->minimum_confidence, 'explanation' => 'Reference and amount match'], [['kind' => 'amount', 'field' => 'amount', 'source_value' => '100.00', 'target_value' => '100.00', 'weight' => 1]]);
    expect($suggestion->evidence)->toHaveCount(1);
    $suggestion = app(DecideSuggestion::class)->handle($suggestion, 'operator-1', true, true);
    $feedback = app(RecordFeedback::class)->handle($suggestion, 'operator-1', 'correct', 'Confirmed by operator.');
    expect($suggestion->status)->toBe(SuggestionStatus::Automated)->and($feedback->feedback_type->value)->toBe('correct');
});

it('rejects unsafe automation and is idempotent by suggestion reference', function (): void {
    $data = ['team_id' => 1, 'suggestion_ref' => 'MATCH-2', 'source_type' => 'document', 'source_id' => 'doc-1', 'target_type' => 'settlement', 'target_id' => 'settle-1', 'match_type' => 'document-settlement', 'confidence' => .7, 'automation_threshold' => .8];
    $first = app(CreateSuggestion::class)->handle($data);
    $same = app(CreateSuggestion::class)->handle($data);
    expect($same->id)->toBe($first->id);
    expect(fn (): mixed => app(DecideSuggestion::class)->handle($first, 'operator-1', true, true))->toThrow(InvalidMatch::class);
    expect(app(MatchingQuery::class)->automationEligible(1)->total())->toBe(0);
});

it('exposes authenticated matching intelligence API routes', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.matching-intelligence.read', 'accounting.matching-intelligence.write']);
    $this->postJson('/api/v1/accounting/matching-intelligence/suggestions', ['team_id' => 1, 'suggestion_ref' => 'API-MATCH-1', 'source_type' => 'bank', 'source_id' => 'b-1', 'target_type' => 'payment', 'target_id' => 'p-1', 'match_type' => 'bank-payment', 'confidence' => .88])->assertCreated()->assertJsonPath('data.type', 'accounting-matching-suggestion');
    $this->getJson('/api/v1/accounting/matching-intelligence/suggestions')->assertOk();
});
