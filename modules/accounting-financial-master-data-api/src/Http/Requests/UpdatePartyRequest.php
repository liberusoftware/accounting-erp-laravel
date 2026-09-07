<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdatePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'reference' => ['sometimes', 'nullable', 'string', 'max:64'], 'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'], 'phone' => ['sometimes', 'nullable', 'string', 'max:64'],
            'tax_identifier' => ['sometimes', 'nullable', 'string', 'max:128'], 'payment_term_id' => ['sometimes', 'nullable', 'integer', 'exists:accounting_master_payment_terms,id'],
            'credit_limit' => ['sometimes', 'nullable', 'numeric', 'min:0'], 'status' => ['sometimes', 'in:active,inactive'], 'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
