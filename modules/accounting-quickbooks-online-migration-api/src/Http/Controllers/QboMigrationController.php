<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigrationApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\QuickBooksOnlineMigration\Actions\CreateMigrationRun;
use Liberu\Accounting\QuickBooksOnlineMigration\Actions\ImportMigrationRecords;
use Liberu\Accounting\QuickBooksOnlineMigration\Actions\ReconcileMigration;
use Liberu\Accounting\QuickBooksOnlineMigration\Models\QboMigrationRun;

final class QboMigrationController extends Controller
{
    public function index(): mixed
    {
        return QboMigrationRun::query()->latest()->paginate(25);
    }

    public function store(Request $request, CreateMigrationRun $create, ImportMigrationRecords $import): QboMigrationRun
    {
        $data = $request->validate(['connection_id' => 'nullable|integer', 'metadata' => 'nullable|array', 'records' => 'required|array']);

        return $import->handle($create->handle($data['connection_id'] ?? null, $data['metadata'] ?? []), $data['records']);
    }

    public function show(QboMigrationRun $run): QboMigrationRun
    {
        return $run->load('records');
    }

    public function reconcile(QboMigrationRun $run, ReconcileMigration $action): QboMigrationRun
    {
        return $action->handle($run);
    }
}
