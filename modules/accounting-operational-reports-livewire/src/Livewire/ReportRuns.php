<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReportsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\OperationalReports\Queries\ReportQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class ReportRuns extends Component
{
    use WithPagination;

    public string $category = '';

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view operational reports.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-operational-reports::report-runs', ['runs' => app(ReportQuery::class)->paginate(auth()->user()?->current_team_id, $this->category ?: null, $this->status ?: null)]);
    }
}
