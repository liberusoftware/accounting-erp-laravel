<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\TimeTracking\Actions\ApproveTimeEntry;
use Liberu\Accounting\TimeTracking\Actions\CreateTimeEntry;
use Liberu\Accounting\TimeTracking\Actions\StartTimer;
use Liberu\Accounting\TimeTracking\Actions\StopTimer;
use Liberu\Accounting\TimeTracking\Actions\SubmitTimeEntry;
use Liberu\Accounting\TimeTracking\Enums\TimeEntryStatus;
use Liberu\Accounting\TimeTracking\Enums\TimerStatus;
use Liberu\Accounting\TimeTracking\Exceptions\InvalidTimeEntry;

uses(RefreshDatabase::class);

it('supports time entry approval and timer lifecycle', function (): void {
    $entry = app(CreateTimeEntry::class)->handle(['team_id' => 11, 'worker_ref' => 'worker-1', 'worked_on' => '2026-08-29', 'hours' => 7.5, 'project_ref' => 'project-1']);
    app(SubmitTimeEntry::class)->handle($entry);
    app(ApproveTimeEntry::class)->handle($entry->fresh());

    $timer = app(StartTimer::class)->handle(['team_id' => 11, 'worker_ref' => 'worker-1', 'project_ref' => 'project-1']);
    app(StopTimer::class)->handle($timer);

    expect($entry->fresh()->status)->toBe(TimeEntryStatus::Approved)
        ->and($timer->fresh()->status)->toBe(TimerStatus::Stopped)
        ->and($timer->fresh()->stopped_at)->not->toBeNull();
});

it('rejects invalid durations', function (): void {
    expect(fn (): mixed => app(CreateTimeEntry::class)->handle(['team_id' => 11, 'worker_ref' => 'worker-1', 'worked_on' => '2026-08-29', 'hours' => 25]))->toThrow(InvalidTimeEntry::class);
});
