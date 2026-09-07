<?php

namespace Liberu\Accounting\PoliciesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\Policies\Actions\SavePolicyRule;
use Liberu\Accounting\Policies\Models\PolicyRule;
use Livewire\Component;

final class PolicyRules extends Component
{
    public string $bookId = '';

    public string $category = 'recognition';

    public string $key = '';

    public string $value = '{}';

    public string $effectiveFrom = '';

    public string $effectiveUntil = '';

    public function save(SavePolicyRule $save): void
    {
        $d = $this->validate(['bookId' => ['required', 'exists:accounting_books,id'], 'category' => ['required', 'in:recognition,capitalization,depreciation,fx,tax,rounding,write_off,materiality,approval'], 'key' => ['required', 'string', 'max:100'], 'value' => ['required', 'json'], 'effectiveFrom' => ['required', 'date'], 'effectiveUntil' => ['nullable', 'date', 'after_or_equal:effectiveFrom']]);
        $decoded = json_decode($d['value'], true, flags: JSON_THROW_ON_ERROR);
        abort_unless(is_array($decoded), 422);
        $save->handle(null, ['book_id' => $d['bookId'], 'category' => $d['category'], 'key' => $d['key'], 'value' => $decoded, 'effective_from' => $d['effectiveFrom'], 'effective_until' => $d['effectiveUntil'] ?: null, 'is_active' => true]);
        $this->reset('key', 'effectiveFrom', 'effectiveUntil');
        $this->value = '{}';
        $this->dispatch('policy-rule-saved');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-policies-livewire::livewire.policy-rules', ['policyRules' => PolicyRule::query()->latest('effective_from')->paginate(15)]);
    }
}
