<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateLegalEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'registration_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'currency_code' => ['sometimes', 'required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'accounting_basis' => ['sometimes', 'required', 'in:accrual,cash'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
