<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CodingSuggestions\Actions\ApplySuggestionFeedback;
use Liberu\Accounting\CodingSuggestions\Actions\CreateCodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Actions\ReviewCodingSuggestion;
use Liberu\Accounting\CodingSuggestions\Actions\SetSuggestionPolicy;
use Liberu\Accounting\CodingSuggestions\Enums\CodingSuggestionStatus;
use Liberu\Accounting\CodingSuggestions\Exceptions\InvalidCodingSuggestion;

uses(RefreshDatabase::class);

it('captures feedback, policy and review for a coding suggestion', function (): void {
    $suggestion = app(CreateCodingSuggestion::class)->handle(['team_id' => 606, 'source_ref' => 'txn-1', 'target_type' => 'account', 'target_ref' => 'account-1', 'confidence' => .92, 'explanation' => 'Matched payee history']);
    app(SetSuggestionPolicy::class)->handle($suggestion, ['minimum_confidence' => .8]);
    app(ApplySuggestionFeedback::class)->handle($suggestion->refresh(), ['decision' => 'accepted']);
    $reviewed = app(ReviewCodingSuggestion::class)->handle($suggestion->refresh(), ['reviewer_ref' => 'user-1']);

    expect($reviewed->status)->toBe(CodingSuggestionStatus::Reviewed)
        ->and($reviewed->policy['minimum_confidence'])->toBe(.8)
        ->and($reviewed->feedback['decision'])->toBe('accepted');
});

it('rejects confidence outside the policy range', function (): void {
    expect(fn () => app(CreateCodingSuggestion::class)->handle(['team_id' => 606, 'source_ref' => 'txn-2', 'target_type' => 'tax', 'target_ref' => 'tax-1', 'confidence' => 1.1, 'explanation' => 'Invalid']))
        ->toThrow(InvalidCodingSuggestion::class);
});
