<?php

namespace Liberu\Accounting\PeriodsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\Periods\Actions\CreatePeriod;
use Liberu\Accounting\Periods\Actions\TransitionPeriod;
use Liberu\Accounting\Periods\Enums\PeriodState;
use Liberu\Accounting\Periods\Models\AccountingPeriod;
use Livewire\Component;

final class Periods extends Component
{
    public int|string $bookId = '';

    public string $startsOn = '';

    public string $endsOn = '';

    public string $state = 'open';

    public string $reason = '';

    public function save(CreatePeriod $create): void
    {
        $data = $this->validate(['bookId' => ['required', 'exists:accounting_books,id'], 'startsOn' => ['required', 'date'], 'endsOn' => ['required', 'date', 'after_or_equal:startsOn']]);
        $create->handle(['book_id' => $data['bookId'], 'starts_on' => $data['startsOn'], 'ends_on' => $data['endsOn']]);
        $this->reset('startsOn', 'endsOn');
        $this->dispatch('period-created');
    }

    public function changeState(int $periodId, TransitionPeriod $transition): void
    {
        $period = AccountingPeriod::findOrFail($periodId);
        $transition->handle($period, PeriodState::from($this->state), auth()->id() === null ? null : (string) auth()->id(), $this->reason ?: null);
        $this->dispatch('period-transitioned');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-periods-livewire::livewire.periods', ['periods' => AccountingPeriod::query()->latest()->paginate(15)]);
    }
}
