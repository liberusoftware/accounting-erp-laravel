<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCaptureLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Queries\CaptureQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Documents extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view captured documents.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('accounting-document-capture-livewire::documents', ['documents' => app(CaptureQuery::class)->paginate(auth()->user()?->current_team_id, $this->status !== '' ? CaptureStatus::tryFrom($this->status) : null)]);
    }
}
