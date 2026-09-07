<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StorePartyDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        if ($this->route('detail') === 'addresses') {
            return ['kind' => ['sometimes', 'string', 'max:32'], 'line_one' => ['required', 'string', 'max:255'], 'line_two' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:128'], 'region' => ['nullable', 'string', 'max:128'], 'postal_code' => ['nullable', 'string', 'max:32'], 'country_code' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'], 'is_primary' => ['sometimes', 'boolean'], 'metadata' => ['nullable', 'array']];
        }

        return ['label' => ['required', 'string', 'max:128'], 'account_name' => ['nullable', 'string', 'max:255'], 'bank_name' => ['nullable', 'string', 'max:255'], 'country_code' => ['nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'], 'currency_code' => ['nullable', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'], 'masked_account' => ['nullable', 'string', 'max:32'], 'credential_reference' => ['required', 'string', 'max:255'], 'is_primary' => ['sometimes', 'boolean'], 'metadata' => ['nullable', 'array']];
    }
}
