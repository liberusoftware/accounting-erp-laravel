<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreReferenceDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return match ($this->route('resource')) {
            'items-services' => ['legal_entity_id' => ['required', 'integer', 'exists:accounting_legal_entities,id'], 'sku' => ['required', 'string', 'max:64'], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'kind' => ['required', 'in:item,service'], 'unit' => ['nullable', 'string', 'max:32'], 'sales_price' => ['nullable', 'numeric', 'min:0'], 'purchase_price' => ['nullable', 'numeric', 'min:0'], 'tax_profile_id' => ['nullable', 'integer', 'exists:accounting_master_tax_profiles,id'], 'metadata' => ['nullable', 'array']],
            'tax-profiles' => ['legal_entity_id' => ['required', 'integer', 'exists:accounting_legal_entities,id'], 'code' => ['required', 'string', 'max:64'], 'name' => ['required', 'string', 'max:255'], 'rate' => ['required', 'numeric', 'min:0', 'max:100'], 'inclusive' => ['sometimes', 'boolean'], 'recoverable' => ['sometimes', 'boolean'], 'metadata' => ['nullable', 'array']],
            'payment-terms' => ['legal_entity_id' => ['required', 'integer', 'exists:accounting_legal_entities,id'], 'code' => ['required', 'string', 'max:64'], 'name' => ['required', 'string', 'max:255'], 'days' => ['required', 'integer', 'min:0'], 'metadata' => ['nullable', 'array']],
            default => [],
        };
    }
}
