<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Liberu\Accounting\Core\Enums\AccountingBasis;

final class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['legal_entity_id' => ['required', 'integer', 'exists:accounting_legal_entities,id'], 'name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50'], 'accounting_basis' => ['required', Rule::enum(AccountingBasis::class)], 'is_active' => ['sometimes', 'boolean']];
    }
}
