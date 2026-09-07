<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreLivewire\Livewire;

use Liberu\Accounting\TaxCore\Queries\TaxRuleQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class TaxRules extends Component
{
    use WithPagination;

    public string $status = '';

    public function render(): mixed
    {
        abort_unless(auth()->check(), 403);

        return view('module-accounting-tax-core::tax-rules', ['rules' => app(TaxRuleQuery::class)->paginate($this->status ?: null)]);
    }
}
