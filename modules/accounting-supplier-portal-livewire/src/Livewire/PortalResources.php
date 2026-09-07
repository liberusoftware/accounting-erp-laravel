<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortalLivewire\Livewire;

use Liberu\Accounting\SupplierPortal\Queries\PortalResourceQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class PortalResources extends Component
{
    use WithPagination;

    public string $status = '';

    public function render(): mixed
    {
        abort_unless(auth()->check(), 403);

        return view('module-accounting-supplier-portal::resources', ['resources' => app(PortalResourceQuery::class)->paginate(null, null, $this->status ?: null)]);
    }
}
