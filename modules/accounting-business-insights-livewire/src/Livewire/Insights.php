<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsLivewire\Livewire;

use Liberu\Accounting\BusinessInsights\Queries\InsightQuery;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class Insights extends Component
{
    use WithPagination;

    #[Url]
    public string $metric = '';

    public function render(): mixed
    {
        return view('accounting-business-insights::insights', ['insights' => app(InsightQuery::class)->paginate((int) (auth()->user()?->current_team_id ?? -1), $this->metric ?: null)]);
    }
}
