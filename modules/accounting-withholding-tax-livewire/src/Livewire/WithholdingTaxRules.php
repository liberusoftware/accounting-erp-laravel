<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTaxLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxRule;
use Livewire\Component;
use Livewire\WithPagination;

final class WithholdingTaxRules extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view withholding tax rules.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-withholding-tax::rules', ['rules' => WithholdingTaxRule::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest()->paginate(15)]);
    }
}
