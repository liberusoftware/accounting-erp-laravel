<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\FinancialMasterData\Actions\SaveReferenceData;
use Liberu\Accounting\FinancialMasterData\Models\ItemService;
use Liberu\Accounting\FinancialMasterData\Models\PaymentTerm;
use Liberu\Accounting\FinancialMasterData\Models\TaxProfile;
use Livewire\Component;

final class ReferenceData extends Component
{
    public string $resource = 'items-services';

    public string $legalEntityId = '';

    public string $code = '';

    public string $name = '';

    public string $sku = '';

    public string $rate = '';

    public string $days = '0';

    public function save(SaveReferenceData $save): void
    {
        $data = $this->validate(['resource' => ['required', 'in:items-services,tax-profiles,payment-terms'], 'legalEntityId' => ['required', 'integer', 'exists:accounting_legal_entities,id'], 'code' => ['nullable', 'string', 'max:64'], 'name' => ['required', 'string', 'max:255'], 'sku' => ['nullable', 'string', 'max:64'], 'rate' => ['nullable', 'numeric', 'min:0', 'max:100'], 'days' => ['nullable', 'integer', 'min:0']]);
        [$class, $attributes] = match ($data['resource']) {
            'tax-profiles' => [TaxProfile::class, ['legal_entity_id' => $data['legalEntityId'], 'code' => $data['code'], 'name' => $data['name'], 'rate' => $data['rate'] ?: 0]],
            'payment-terms' => [PaymentTerm::class, ['legal_entity_id' => $data['legalEntityId'], 'code' => $data['code'], 'name' => $data['name'], 'days' => $data['days'] ?: 0]],
            default => [ItemService::class, ['legal_entity_id' => $data['legalEntityId'], 'sku' => $data['sku'] ?: $data['code'], 'name' => $data['name'], 'kind' => 'service']],
        };
        $save->handle($class, $attributes);
        $this->reset('code', 'name', 'sku', 'rate', 'days');
        $this->dispatch('reference-data-created');
    }

    public function render(): View
    {
        $class = match ($this->resource) {
            'tax-profiles' => TaxProfile::class, 'payment-terms' => PaymentTerm::class, default => ItemService::class
        };

        return ViewFacade::make('accounting-financial-master-data-livewire::livewire.reference-data', ['records' => $class::query()->where('legal_entity_id', $this->legalEntityId ?: 0)->latest()->paginate(25)]);
    }
}
