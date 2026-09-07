<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReportsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\OperationalReports\Actions\GenerateReport;
use Liberu\Accounting\OperationalReports\Actions\PublishReport;
use Liberu\Accounting\OperationalReports\Models\ReportRun;
use Liberu\Accounting\OperationalReports\Queries\ReportQuery;
use Liberu\Accounting\OperationalReportsApi\Http\Resources\ReportResource;

final class OperationalReportsController extends Controller
{
    public function index(Request $request, ReportQuery $query): mixed
    {
        return ReportResource::collection($query->paginate($request->integer('team_id') ?: null, $request->string('category')->toString() ?: null, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, GenerateReport $action): JsonResponse
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'report_key' => 'required|string|max:120', 'name' => 'required|string|max:190', 'category' => 'required|in:receivables_payables,sales_purchases,tax,bank,inventory,assets,expenses,projects,payroll,exceptions', 'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'currency' => 'nullable|string|size:3', 'filters' => 'nullable|array', 'rows' => 'nullable|array', 'rows.*.row_key' => 'required|string|max:190', 'rows.*.label' => 'nullable|string|max:255', 'rows.*.amount' => 'nullable|numeric', 'rows.*.currency' => 'nullable|string|size:3', 'rows.*.payload' => 'nullable|array', 'exceptions' => 'nullable|array', 'exceptions.*.exception_key' => 'required|string|max:120', 'exceptions.*.message' => 'required|string']);
        $run = $action->handle($data, $data['rows'] ?? [], $data['exceptions'] ?? []);

        return (new ReportResource($run))->response()->setStatusCode(201);
    }

    public function show(ReportRun $reportRun): ReportResource
    {
        return new ReportResource($reportRun->load('rows', 'exceptions'));
    }

    public function publish(Request $request, ReportRun $reportRun, PublishReport $action): ReportResource
    {
        return new ReportResource($action->handle($reportRun, (int) $request->user()->getAuthIdentifier()));
    }

    public function exceptions(Request $request, ReportQuery $query): mixed
    {
        return ReportResource::collection($query->exceptions($request->integer('team_id') ?: null));
    }
}
