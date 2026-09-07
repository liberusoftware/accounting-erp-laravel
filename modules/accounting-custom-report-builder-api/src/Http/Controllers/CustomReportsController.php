<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilderApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CustomReportBuilder\Actions\CreateCustomReport;
use Liberu\Accounting\CustomReportBuilder\Actions\RequestReportExport;
use Liberu\Accounting\CustomReportBuilder\Actions\SaveReportVariant;
use Liberu\Accounting\CustomReportBuilder\Models\CustomReport;
use Liberu\Accounting\CustomReportBuilder\Queries\CustomReportQuery;

final class CustomReportsController extends Controller
{
    public function index(CustomReportQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCustomReport $action): JsonResponse
    {
        $data = $request->validate(['report_ref' => ['required', 'string', 'max:160'], 'name' => ['required', 'string', 'max:160'], 'measures' => ['required', 'array', 'min:1'], 'dimensions' => ['nullable', 'array'], 'filters' => ['nullable', 'array'], 'grouping' => ['nullable', 'array'], 'formulas' => ['nullable', 'array'], 'comparisons' => ['nullable', 'array'], 'layout' => ['nullable', 'array'], 'permissions' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function variant(Request $request, string $report, SaveReportVariant $action): JsonResponse
    {
        $model = CustomReport::query()->where('team_id', $this->teamId())->findOrFail($report);
        $data = $request->validate(['variant_ref' => ['required', 'string', 'max:160'], 'configuration' => ['required', 'array']]);

        return response()->json(['data' => $action->handle($model, $data['variant_ref'], $data['configuration'])], 201);
    }

    public function export(Request $request, string $report, RequestReportExport $action): JsonResponse
    {
        $model = CustomReport::query()->where('team_id', $this->teamId())->findOrFail($report);
        $data = $request->validate(['format' => ['required', 'in:csv,xlsx,pdf'], 'parameters' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle($model, $data['format'], $data['parameters'] ?? [])], 201);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}
