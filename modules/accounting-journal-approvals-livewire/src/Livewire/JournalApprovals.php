<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovalsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus;
use Liberu\Accounting\JournalApprovals\Queries\JournalApprovalQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class JournalApprovals extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view journal approvals.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-journal-approvals-livewire::approvals', ['approvals' => app(JournalApprovalQuery::class)->paginate(auth()->user()?->current_team_id, $this->status !== '' ? ApprovalStatus::from($this->status) : null)]);
    }
}
