<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCaptureLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\DocumentCaptureLivewire\Livewire\Documents;
use Livewire\Livewire;

final class DocumentCaptureLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-document-capture-livewire');
        Livewire::component('module-accounting-document-capture::documents', Documents::class);
        Livewire::component('document-capture', Documents::class);
    }
}
