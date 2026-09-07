<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigrationApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\SpreadsheetMigration\Actions\CreateMigrationRun;
use Liberu\Accounting\SpreadsheetMigration\Actions\CreateMigrationTemplate;
use Liberu\Accounting\SpreadsheetMigration\Actions\ValidateMigration;
use Liberu\Accounting\SpreadsheetMigration\Models\MigrationRun;
use Liberu\Accounting\SpreadsheetMigration\Models\MigrationTemplate;
use Liberu\Accounting\SpreadsheetMigrationApi\Http\Resources\MigrationRunResource;

final class MigrationController extends Controller
{
    public function templates(): mixed
    {
        return MigrationTemplate::query()->orderBy('name')->paginate(25);
    }

    public function storeTemplate(Request $request, CreateMigrationTemplate $action): MigrationTemplate
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'entity' => 'required|string|max:80', 'mapping' => 'required|array', 'metadata' => 'nullable|array']);

        return $action->handle($data);
    }

    public function storeRun(Request $request, MigrationTemplate $template, CreateMigrationRun $action): MigrationRunResource
    {
        $data = $request->validate(['mode' => 'required|string', 'rows' => 'required|array', 'source_total' => 'numeric', 'target_total' => 'numeric', 'metadata' => 'nullable|array']);

        return new MigrationRunResource($action->handle($template, $data));
    }

    public function validateRun(MigrationRun $run, ValidateMigration $action): MigrationRunResource
    {
        return new MigrationRunResource($action->handle($run));
    }
}
