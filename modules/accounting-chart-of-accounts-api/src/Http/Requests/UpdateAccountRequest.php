<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:accounting_chart_accounts,id'],
            'code' => ['sometimes', 'required', 'string', 'max:64'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'type' => ['sometimes', 'required', 'in:asset,liability,equity,revenue,expense'],
            'normal_balance' => ['sometimes', 'nullable', 'in:debit,credit'],
            'is_control_account' => ['sometimes', 'boolean'],
            'allow_manual_entry' => ['sometimes', 'boolean'],
            'locale' => ['sometimes', 'nullable', 'string', 'max:16'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
