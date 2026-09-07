<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\ProductAndServiceItems\Models\AccountingItem;
use Livewire\Component;

final class AccountingItems extends Component
{
    public string $search = '';

    public function render(): View
    {
        return ViewFacade::make('accounting-product-and-service-items-livewire::livewire.accounting-items', ['items' => $this->query()->when(trim($this->search) !== '', fn (Builder $query): Builder => $query->where(fn (Builder $nested): Builder => $nested->where('code', 'like', '%'.trim($this->search).'%')->orWhere('name', 'like', '%'.trim($this->search).'%')))->orderBy('code')->paginate(15)]);
    }

    private function query(): Builder
    {
        return AccountingItem::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->where('status', 'active');
    }
}
