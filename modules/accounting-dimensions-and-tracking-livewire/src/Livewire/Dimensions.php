<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\Dimensions\Models\Dimension;
use Livewire\Component;

final class Dimensions extends Component
{
    public function render(): View
    {
        return ViewFacade::make('accounting-dimensions-and-tracking-livewire::dimensions', ['dimensions' => Dimension::withCount('values')->where('is_active', true)->orderBy('kind')->paginate(25)]);
    }
}
