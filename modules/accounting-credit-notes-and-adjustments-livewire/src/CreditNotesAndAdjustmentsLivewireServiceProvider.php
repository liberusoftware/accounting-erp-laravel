<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustmentsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CreditNotesAndAdjustmentsLivewire\Livewire\CreditNoteOverview;
use Livewire\Livewire;

final class CreditNotesAndAdjustmentsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-credit-notes');
        Livewire::component('module-accounting-credit-notes-and-adjustments', CreditNoteOverview::class);
    }
}
