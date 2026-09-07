<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ClientCollaboration\Actions\CreateCollaborationThread;
use Liberu\Accounting\ClientCollaboration\Actions\RecordCollaborationActivity;
use Liberu\Accounting\ClientCollaboration\Actions\ResolveCollaboration;
use Liberu\Accounting\ClientCollaboration\Enums\CollaborationStatus;
use Liberu\Accounting\ClientCollaboration\Exceptions\InvalidCollaboration;

uses(RefreshDatabase::class);

it('records collaboration activity and resolves an approval thread', function (): void {
    $thread = app(CreateCollaborationThread::class)->handle(['team_id' => 808, 'thread_ref' => 'thread-1', 'kind' => 'document-request', 'subject' => 'Annual accounts']);
    $activity = app(RecordCollaborationActivity::class);
    $activity->message($thread, ['body' => 'Please upload the signed accounts.']);
    $activity->evidence($thread->refresh(), ['reference' => 'document-1']);
    $activity->approval($thread->refresh(), ['approver_ref' => 'client-1']);
    $resolver = app(ResolveCollaboration::class);
    $resolver->approve($thread->refresh(), 'client-1');
    $closed = $resolver->close($thread->refresh());

    expect($closed->status)->toBe(CollaborationStatus::Closed)
        ->and($closed->messages)->toHaveCount(1)
        ->and($closed->evidence)->toHaveCount(1);
});

it('rejects unsupported collaboration kinds and messages on closed threads', function (): void {
    expect(fn () => app(CreateCollaborationThread::class)->handle(['team_id' => 808, 'thread_ref' => 'thread-2', 'kind' => 'unsupported', 'subject' => 'Invalid']))
        ->toThrow(InvalidCollaboration::class);
});
