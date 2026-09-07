<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreFiscalCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date', 'after_or_equal:starts_on'], 'is_closed' => ['sometimes', 'boolean']];
    }
}
