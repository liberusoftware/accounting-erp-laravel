<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StorePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'legal_entity_id' => ['required', 'integer', 'exists:accounting_legal_entities,id'],
            'type' => ['required', 'in:customer,supplier'], 'reference' => ['nullable', 'string', 'max:64'],
            'name' => ['required', 'string', 'max:255'], 'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:64'],
            'tax_identifier' => ['nullable', 'string', 'max:128'], 'payment_term_id' => ['nullable', 'integer', 'exists:accounting_master_payment_terms,id'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'], 'metadata' => ['nullable', 'array'],
        ];
    }
}
