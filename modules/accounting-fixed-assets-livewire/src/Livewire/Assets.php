<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssetsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Queries\AssetQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Assets extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view fixed assets.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        $status = $this->status === '' ? null : AssetStatus::tryFrom($this->status);

        return view('module-accounting-fixed-assets-livewire::assets', [
            'assets' => app(AssetQuery::class)->paginate(auth()->user()?->current_team_id, $status),
        ]);
    }
}
