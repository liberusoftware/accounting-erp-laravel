<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\PayrollJournals\Actions\PostPayrollJournal;
use Liberu\Accounting\PayrollJournals\Actions\ReversePayrollJournal;
use Liberu\Accounting\PayrollJournals\Models\PayrollJournal;
use Livewire\Component;

final class PayrollJournals extends Component
{
    public int|string $selectedJournalId = '';

    public string $reversalRef = '';

    public function selectJournal(int $journalId): void
    {
        $this->selectedJournalId = $journalId;
    }

    public function post(PostPayrollJournal $action): void
    {
        $action->handle($this->currentJournal());
        $this->reset('selectedJournalId');
        $this->dispatch('payroll-journal-posted');
    }

    public function reverse(ReversePayrollJournal $action): void
    {
        $this->validate(['selectedJournalId' => ['required', 'integer'], 'reversalRef' => ['required', 'string', 'max:100']]);
        $action->handle($this->currentJournal(), $this->reversalRef);
        $this->reset('selectedJournalId', 'reversalRef');
        $this->dispatch('payroll-journal-reversed');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-payroll-journals-livewire::livewire.payroll-journals', ['journals' => $this->query()->latest()->paginate(15)]);
    }

    private function query(): Builder
    {
        return PayrollJournal::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    private function currentJournal(): PayrollJournal
    {
        $this->validate(['selectedJournalId' => ['required', 'integer']]);

        return $this->query()->findOrFail($this->selectedJournalId);
    }
}
