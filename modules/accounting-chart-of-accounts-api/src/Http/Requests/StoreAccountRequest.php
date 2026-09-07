<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreAccountRequest extends FormRequest
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
            'parent_id' => ['nullable', 'integer', 'exists:accounting_chart_accounts,id'],
            'code' => ['required', 'string', 'max:64'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'normal_balance' => ['nullable', 'in:debit,credit'],
            'is_control_account' => ['sometimes', 'boolean'],
            'allow_manual_entry' => ['sometimes', 'boolean'],
            'locale' => ['nullable', 'string', 'max:16'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
