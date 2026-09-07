<?php

namespace Liberu\Accounting\CoreLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\Core\Actions\SaveAccountingSetting;
use Liberu\Accounting\Core\Models\AccountingDefault;
use Liberu\Accounting\Core\Models\AccountingPolicy;
use Liberu\Accounting\Core\Models\Book;
use Livewire\Component;

final class AccountingSettings extends Component
{
    public int|string $bookId = '';

    public string $setting = 'defaults';

    public string $key = '';

    public string $value = '{}';

    public function save(SaveAccountingSetting $save): void
    {
        $data = $this->validate([
            'bookId' => ['required', 'exists:accounting_books,id'],
            'setting' => ['required', 'in:defaults,policies'],
            'key' => ['required', 'string', 'max:100'],
            'value' => ['required', 'json'],
        ]);
        $class = $data['setting'] === 'defaults' ? AccountingDefault::class : AccountingPolicy::class;
        $decoded = json_decode($data['value'], true, flags: JSON_THROW_ON_ERROR);
        abort_unless(is_array($decoded), 422);
        $save->handle($class, null, ['book_id' => $data['bookId'], 'key' => $data['key'], 'value' => $decoded]);
        $this->reset('key');
        $this->value = '{}';
        $this->dispatch('accounting-setting-saved');
    }

    public function render(): View
    {
        $class = $this->setting === 'defaults' ? AccountingDefault::class : AccountingPolicy::class;

        return ViewFacade::make('accounting-core-livewire::livewire.accounting-settings', [
            'books' => Book::query()->orderBy('name')->get(),
            'settings' => $class::query()->with('book')->latest()->paginate(15),
        ]);
    }
}
