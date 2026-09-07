<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReportingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ContractorReporting\Actions\CreateContractorReport;
use Liberu\Accounting\ContractorReporting\Actions\FileContractorReport;
use Liberu\Accounting\ContractorReporting\Actions\ValidateContractorReport;
use Liberu\Accounting\ContractorReporting\Models\ContractorReport;
use Liberu\Accounting\ContractorReporting\Queries\ContractorReportQuery;

final class ContractorReportsController extends Controller
{
    public function index(ContractorReportQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateContractorReport $action): JsonResponse
    {
        $data = $request->validate(['payee_ref' => ['required', 'string', 'max:160'], 'tax_year' => ['required', 'digits:4'], 'classification' => ['required', 'string', 'max:80'], 'threshold' => ['required', 'numeric', 'gte:0'], 'reportable_amount' => ['nullable', 'numeric', 'gte:0'], 'form_type' => ['required', 'string', 'max:40'], 'evidence' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function validateReport(Request $request, string $report, ValidateContractorReport $action): JsonResponse
    {
        $model = ContractorReport::query()->where('team_id', $this->teamId())->findOrFail($report);
        $data = $request->validate(['tax_id' => ['required', 'string', 'max:80'], 'legal_name' => ['required', 'string', 'max:200']]);

        return response()->json(['data' => $action->handle($model, $data)]);
    }

    public function file(Request $request, string $report, FileContractorReport $action): JsonResponse
    {
        $model = ContractorReport::query()->where('team_id', $this->teamId())->findOrFail($report);
        $data = $request->validate(['adapter' => ['required', 'string', 'max:120']]);

        return response()->json(['data' => $action->handle($model, $data['adapter'])]);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}
