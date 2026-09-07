<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorComplianceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ContractorCompliance\Actions\ExportContractorCompliance;
use Liberu\Accounting\ContractorCompliance\Actions\IssueContractorStatement;
use Liberu\Accounting\ContractorCompliance\Actions\RecordContractorEvidence;
use Liberu\Accounting\ContractorCompliance\Actions\RegisterContractor;
use Liberu\Accounting\ContractorCompliance\Models\Contractor;
use Liberu\Accounting\ContractorCompliance\Queries\ContractorQuery;

final class ContractorsController extends Controller
{
    public function index(ContractorQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, RegisterContractor $action): JsonResponse
    {
        $data = $request->validate(['contractor_ref' => ['required', 'string', 'max:160'], 'legal_name' => ['required', 'string', 'max:200'], 'classification' => ['required', 'string', 'max:80'], 'withholding_scheme' => ['nullable', 'string', 'max:120']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function evidence(Request $request, string $contractor, RecordContractorEvidence $action): JsonResponse
    {
        $model = $this->contractor($contractor);

        return response()->json(['data' => $action->handle($model, $request->validate(['type' => ['required', 'string', 'max:80'], 'reference' => ['required', 'string', 'max:160']]))]);
    }

    public function statement(Request $request, string $contractor, IssueContractorStatement $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->contractor($contractor), $request->validate(['period' => ['required', 'string', 'max:40'], 'amount' => ['nullable', 'numeric', 'gte:0']]))]);
    }

    public function export(Request $request, string $contractor, ExportContractorCompliance $action): JsonResponse
    {
        $data = $request->validate(['region' => ['required', 'string', 'max:80']]);

        return response()->json(['data' => $action->handle($this->contractor($contractor), $data['region'])]);
    }

    private function contractor(string $id): Contractor
    {
        return Contractor::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}
