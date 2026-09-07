<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateReferenceDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return match ($this->route('resource')) {
            'items-services' => ['sku' => ['sometimes', 'required', 'string', 'max:64'], 'name' => ['sometimes', 'required', 'string', 'max:255'], 'description' => ['sometimes', 'nullable', 'string'], 'kind' => ['sometimes', 'required', 'in:item,service'], 'unit' => ['sometimes', 'nullable', 'string', 'max:32'], 'sales_price' => ['sometimes', 'nullable', 'numeric', 'min:0'], 'purchase_price' => ['sometimes', 'nullable', 'numeric', 'min:0'], 'tax_profile_id' => ['sometimes', 'nullable', 'integer', 'exists:accounting_master_tax_profiles,id'], 'status' => ['sometimes', 'in:active,inactive'], 'metadata' => ['sometimes', 'nullable', 'array']],
            'tax-profiles' => ['code' => ['sometimes', 'required', 'string', 'max:64'], 'name' => ['sometimes', 'required', 'string', 'max:255'], 'rate' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'], 'inclusive' => ['sometimes', 'boolean'], 'recoverable' => ['sometimes', 'boolean'], 'status' => ['sometimes', 'in:active,inactive'], 'metadata' => ['sometimes', 'nullable', 'array']],
            'payment-terms' => ['code' => ['sometimes', 'required', 'string', 'max:64'], 'name' => ['sometimes', 'required', 'string', 'max:255'], 'days' => ['sometimes', 'required', 'integer', 'min:0'], 'status' => ['sometimes', 'in:active,inactive'], 'metadata' => ['sometimes', 'nullable', 'array']],
            default => [],
        };
    }
}
