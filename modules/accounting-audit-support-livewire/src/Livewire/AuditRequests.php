<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupportLivewire\Livewire;

use Liberu\Accounting\AuditSupport\Queries\AuditRequestQuery;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class AuditRequests extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function render(): mixed
    {
        return view('accounting-audit-support::audit-requests', ['requests' => app(AuditRequestQuery::class)->paginate((int) (auth()->user()?->current_team_id ?? -1), $this->status ?: null)]);
    }
}
