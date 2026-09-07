<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEventsLivewire\Livewire;

use Liberu\Accounting\AssetEvents\Queries\AssetEventQuery;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class AssetEvents extends Component
{
    use WithPagination;

    #[Url]
    public string $assetId = '';

    public function render(): mixed
    {
        return view('accounting-asset-events::events', ['events' => app(AssetEventQuery::class)->paginate((int) (auth()->user()?->current_team_id ?? -1), $this->assetId !== '' ? (int) $this->assetId : null)]);
    }
}
