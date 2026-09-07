<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatementsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\FinancialStatements\Exceptions\InvalidStatementRequest;
use Liberu\Accounting\FinancialStatements\Queries\StatementQuery;

final class FinancialStatementsController extends Controller
{
    public function profitAndLoss(Request $request, StatementQuery $query): JsonResponse
    {
        Gate::authorize('accounting.financial-statements.view');
        $data = $this->period($request);

        return $this->data($this->execute(fn (): array => $query->profitAndLoss($data['book_id'], $data['start_date'], $data['end_date'], $data['dimensions'] ?? null)));
    }

    public function balanceSheet(Request $request, StatementQuery $query): JsonResponse
    {
        Gate::authorize('accounting.financial-statements.view');
        $data = $request->validate(['book_id' => ['required', 'integer', 'min:1'], 'as_of_date' => ['required', 'date_format:Y-m-d'], 'dimensions' => ['nullable', 'array']]);

        return $this->data($this->execute(fn (): array => $query->balanceSheet((int) $data['book_id'], $data['as_of_date'], $data['dimensions'] ?? null)));
    }

    public function cashFlow(Request $request, StatementQuery $query): JsonResponse
    {
        Gate::authorize('accounting.financial-statements.view');
        $data = $this->period($request);

        return $this->data($this->execute(fn (): array => $query->cashFlow($data['book_id'], $data['start_date'], $data['end_date'], $data['dimensions'] ?? null)));
    }

    public function equity(Request $request, StatementQuery $query): JsonResponse
    {
        Gate::authorize('accounting.financial-statements.view');
        $data = $this->period($request);

        return $this->data($this->execute(fn (): array => $query->changesInEquity($data['book_id'], $data['start_date'], $data['end_date'], $data['dimensions'] ?? null)));
    }

    public function comparative(Request $request, StatementQuery $query): JsonResponse
    {
        Gate::authorize('accounting.financial-statements.view');
        $data = $request->validate([
            'book_id' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'comparative_start_date' => ['required', 'date_format:Y-m-d'],
            'comparative_end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:comparative_start_date'],
            'dimensions' => ['nullable', 'array'],
        ]);

        return $this->data($this->execute(fn (): array => $query->comparative((int) $data['book_id'], $data['start_date'], $data['end_date'], $data['comparative_start_date'], $data['comparative_end_date'], $data['dimensions'] ?? null)));
    }

    public function drillThrough(Request $request, StatementQuery $query): JsonResponse
    {
        Gate::authorize('accounting.financial-statements.view');
        $data = $request->validate([
            'book_id' => ['required', 'integer', 'min:1'],
            'account_id' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'dimensions' => ['nullable', 'array'],
        ]);

        return $this->data($this->execute(fn (): array => $query->drillThrough((int) $data['book_id'], (int) $data['account_id'], $data['start_date'], $data['end_date'], $data['dimensions'] ?? null)));
    }

    /** @return array{book_id:int,start_date:string,end_date:string,dimensions?:array|null} */
    private function period(Request $request): array
    {
        $data = $request->validate([
            'book_id' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'dimensions' => ['nullable', 'array'],
        ]);

        $data['book_id'] = (int) $data['book_id'];

        return $data;
    }

    private function data(array $data): JsonResponse
    {
        return response()->json(['data' => $data]);
    }

    private function execute(\Closure $query): array
    {
        try {
            return $query();
        } catch (InvalidStatementRequest $exception) {
            throw ValidationException::withMessages(['statement' => $exception->getMessage()]);
        }
    }
}
