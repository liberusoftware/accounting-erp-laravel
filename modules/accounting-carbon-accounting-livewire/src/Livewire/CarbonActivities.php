<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingLivewire\Livewire;

use Liberu\Accounting\CarbonAccounting\Queries\CarbonActivityQuery;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class CarbonActivities extends Component
{
    use WithPagination;

    #[Url]
    public string $scope = '';

    public function render(): mixed
    {
        return view('accounting-carbon-accounting::activities', ['activities' => app(CarbonActivityQuery::class)->paginate((int) (auth()->user()?->current_team_id ?? -1), $this->scope ?: null)]);
    }
}
