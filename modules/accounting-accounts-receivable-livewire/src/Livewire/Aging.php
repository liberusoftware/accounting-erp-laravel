<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\AccountsReceivable\Queries\AgingQuery;
use Livewire\Component;

final class Aging extends Component
{
    public ?int $partyId = null;

    public function mount(?int $partyId = null): void
    {
        $this->partyId = $partyId;
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view receivables.');
        }
    }

    protected function rules(): array
    {
        return ['partyId' => ['nullable', 'integer', 'min:1']];
    }

    public function render(): mixed
    {
        $this->validate();

        return view('accounting-accounts-receivable::aging', ['buckets' => app(AgingQuery::class)->handle($this->partyId)]);
    }
}
