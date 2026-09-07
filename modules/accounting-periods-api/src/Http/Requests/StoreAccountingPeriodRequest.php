<?php

declare(strict_types=1);

namespace Liberu\Accounting\PeriodsApi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreAccountingPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['book_id' => ['required', 'integer', 'exists:accounting_books,id'], 'starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date', 'after_or_equal:starts_on'], 'posting_ends_on' => ['nullable', 'date', 'after_or_equal:starts_on', 'before_or_equal:ends_on']];
    }
}
