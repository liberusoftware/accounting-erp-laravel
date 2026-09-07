<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\AccountsPayable\Queries\AgingQuery;
use Livewire\Component;

final class Aging extends Component
{
    public ?int $partyId = null;

    public function mount(?int $partyId = null): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view payables.');
        }
        $this->partyId = $partyId;
    }

    protected function rules(): array
    {
        return ['partyId' => ['nullable', 'integer', 'min:1']];
    }

    public function render(): mixed
    {
        $this->validate();

        return view('accounting-accounts-payable::aging', ['buckets' => app(AgingQuery::class)->handle($this->partyId)]);
    }
}
