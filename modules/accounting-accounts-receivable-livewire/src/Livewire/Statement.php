<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\AccountsReceivable\Queries\StatementQuery;
use Livewire\Component;

final class Statement extends Component
{
    public int $partyId;

    public ?string $from = null;

    public ?string $to = null;

    public function mount(int $partyId): void
    {
        $this->partyId = $partyId;
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view receivables.');
        }
    }

    protected function rules(): array
    {
        return ['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']];
    }

    public function render(): mixed
    {
        $this->validate();

        return view('accounting-accounts-receivable::statement', ['statement' => app(StatementQuery::class)->handle($this->partyId, $this->from ? new \DateTimeImmutable($this->from) : null, $this->to ? new \DateTimeImmutable($this->to) : null)]);
    }
}
