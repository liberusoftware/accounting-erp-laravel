<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFrameworkLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\MigrationFramework\Queries\MigrationQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class MigrationBatches extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view migration batches.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-migration-framework-livewire::migration-batches', ['batches' => app(MigrationQuery::class)->batches(auth()->user()?->current_team_id)]);
    }
}
