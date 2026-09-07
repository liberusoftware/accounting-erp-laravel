<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreNumberingSequenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['key' => ['required', 'string', 'max:100'], 'prefix' => ['nullable', 'string', 'max:30'], 'next_number' => ['sometimes', 'integer', 'min:1'], 'padding' => ['sometimes', 'integer', 'min:1', 'max:20']];
    }
}
