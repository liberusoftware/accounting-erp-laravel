<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Liberu\Accounting\PayrollIntegration\Actions\ImportPayrollRun;
use Liberu\Accounting\PayrollIntegration\Actions\MarkPayrollImport;
use Liberu\Accounting\PayrollIntegration\Enums\ImportStatus;
use Liberu\Accounting\PayrollIntegration\Models\PayrollImport;
use Liberu\Accounting\PayrollIntegration\Queries\PayrollImportSummary;
use Liberu\Accounting\PayrollIntegrationApi\Http\Resources\PayrollImportResource;

final class PayrollIntegrationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PayrollImportResource::collection(PayrollImport::query()->where('team_id', $this->teamId($request))->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100)));
    }

    public function store(Request $request, ImportPayrollRun $action): JsonResponse
    {
        $data = $request->validate(['provider' => 'required|string|max:100', 'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'run_ref' => 'required|string|max:150', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'employee_refs' => 'nullable|array', 'contractor_refs' => 'nullable|array', 'dimensions' => 'nullable|array', 'project_refs' => 'nullable|array', 'adapter_ref' => 'nullable|string|max:255', 'metadata' => 'nullable|array']);
        $import = $action->handle([...$data, 'team_id' => $this->teamId($request)]);

        return (new PayrollImportResource($import))->response()->setStatusCode(201);
    }

    public function show(Request $request, PayrollImport $payrollImport): PayrollImportResource
    {
        $this->assertTeam($request, $payrollImport);

        return new PayrollImportResource($payrollImport);
    }

    public function status(Request $request, PayrollImport $payrollImport, MarkPayrollImport $action): PayrollImportResource
    {
        $this->assertTeam($request, $payrollImport);

        return new PayrollImportResource($action->handle($payrollImport, ImportStatus::from($request->validate(['status' => 'required|string|in:received,validated,imported,failed,reconciled'])['status'])));
    }

    public function summary(Request $request, PayrollImportSummary $query): array
    {
        return $query->forTeam($this->teamId($request));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, PayrollImport $import): void
    {
        abort_unless((int) $import->team_id === $this->teamId($request), 404);
    }
}
