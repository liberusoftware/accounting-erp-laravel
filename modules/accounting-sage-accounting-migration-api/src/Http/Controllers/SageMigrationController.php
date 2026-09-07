<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigrationApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\SageAccountingMigration\Actions\CreateMigrationRun;
use Liberu\Accounting\SageAccountingMigration\Actions\ImportMigrationRecords;
use Liberu\Accounting\SageAccountingMigration\Actions\ReconcileMigration;
use Liberu\Accounting\SageAccountingMigration\Models\SageMigrationRun;
use Liberu\Accounting\SageAccountingMigrationApi\Http\Resources\SageMigrationRunResource;

final class SageMigrationController extends Controller
{
    public function index(): mixed
    {
        return SageMigrationRun::query()->latest()->paginate(25);
    }

    public function store(Request $request, CreateMigrationRun $create, ImportMigrationRecords $import): SageMigrationRunResource
    {
        $data = $request->validate(['connection_id' => 'nullable|integer', 'metadata' => 'nullable|array', 'records' => 'required|array']);
        $run = $create->handle($data['connection_id'] ?? null, $data['metadata'] ?? []);

        return new SageMigrationRunResource($import->handle($run, $data['records']));
    }

    public function show(SageMigrationRun $run): SageMigrationRunResource
    {
        return new SageMigrationRunResource($run->load('records'));
    }

    public function reconcile(SageMigrationRun $run, ReconcileMigration $action): SageMigrationRunResource
    {
        return new SageMigrationRunResource($action->handle($run));
    }
}
