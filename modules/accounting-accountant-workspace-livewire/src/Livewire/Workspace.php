<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceLivewire\Livewire;

use Liberu\Accounting\AccountantWorkspace\Queries\WorkspaceQuery;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class Workspace extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function render(): mixed
    {
        return view('accounting-accountant-workspace::workspace', ['items' => app(WorkspaceQuery::class)->paginate((int) (auth()->user()?->current_team_id ?? -1), $this->status ?: null)]);
    }
}
