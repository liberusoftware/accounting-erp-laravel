<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;
use Livewire\Component;

final class Journals extends Component
{
    public int $bookId;

    public function mount(int $bookId): void
    {
        $this->bookId = $bookId;
    }

    public function render(): View
    {
        return app('view')->make('accounting-general-ledger-livewire::journals', ['journals' => JournalEntry::with('lines')->where('book_id', $this->bookId)->latest()->paginate(25)]);
    }
}
