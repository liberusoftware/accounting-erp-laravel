<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTaxApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ConstructionTax\Actions\CreateConstructionTaxRecord;
use Liberu\Accounting\ConstructionTax\Actions\SubmitConstructionTaxReturn;
use Liberu\Accounting\ConstructionTax\Actions\VerifySubcontractor;
use Liberu\Accounting\ConstructionTax\Models\ConstructionTaxRecord;
use Liberu\Accounting\ConstructionTax\Queries\ConstructionTaxQuery;

final class ConstructionTaxController extends Controller
{
    public function index(ConstructionTaxQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateConstructionTaxRecord $action): JsonResponse
    {
        $data = $request->validate(['subcontractor_ref' => ['required', 'string', 'max:160'], 'tax_period' => ['required', 'string', 'max:40'], 'deduction_rate' => ['nullable', 'numeric', 'between:0,100'], 'gross_amount' => ['nullable', 'numeric', 'gte:0']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function verify(Request $request, string $record, VerifySubcontractor $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->record($record), $request->validate(['reference' => ['required', 'string', 'max:160'], 'status' => ['nullable', 'string', 'max:40']]))]);
    }

    public function submit(Request $request, string $record, SubmitConstructionTaxReturn $action): JsonResponse
    {
        $data = $request->validate(['adapter' => ['required', 'string', 'max:120']]);

        return response()->json(['data' => $action->handle($this->record($record), $data['adapter'])]);
    }

    private function record(string $id): ConstructionTaxRecord
    {
        return ConstructionTaxRecord::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}
