<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCodingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\CashCoding\Models\CashCodingBatch;
use Livewire\Component;

final class Batches extends Component
{
    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view cash coding.');
        }
    }

    public function render(): mixed
    {
        return view('accounting-cash-coding::batches', ['batches' => CashCodingBatch::query()->where('team_id', auth()->user()->current_team_id)->latest()->get()]);
    }
}
