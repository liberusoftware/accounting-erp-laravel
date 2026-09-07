<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreAccountingSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:100'],
            'value' => ['required', 'array'],
        ];
    }
}
