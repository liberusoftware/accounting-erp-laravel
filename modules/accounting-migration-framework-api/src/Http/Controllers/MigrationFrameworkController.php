<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFrameworkApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\MigrationFramework\Actions\CreateBatch;
use Liberu\Accounting\MigrationFramework\Actions\CreateMapping;
use Liberu\Accounting\MigrationFramework\Actions\ReconcileBatch;
use Liberu\Accounting\MigrationFramework\Actions\RegisterSource;
use Liberu\Accounting\MigrationFramework\Actions\RunBatch;
use Liberu\Accounting\MigrationFramework\Models\MigrationBatch;
use Liberu\Accounting\MigrationFramework\Models\MigrationMapping;
use Liberu\Accounting\MigrationFramework\Models\MigrationSource;
use Liberu\Accounting\MigrationFramework\Queries\MigrationQuery;
use Liberu\Accounting\MigrationFrameworkApi\Http\Resources\MigrationBatchResource;

final class MigrationFrameworkController extends Controller
{
    public function sources(Request $request, MigrationQuery $query): mixed
    {
        return $query->sources($request->integer('team_id') ?: null, $request->integer('per_page', 25));
    }

    public function source(Request $request, RegisterSource $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($request->validate(['team_id' => 'nullable|integer', 'source_ref' => 'required|string|max:190', 'provider' => 'required|string|max:80', 'source_type' => 'required|string|max:80', 'name' => 'required|string|max:190', 'record_count' => 'nullable|integer|min:0', 'checksum' => 'nullable|string|max:128']))], 201);
    }

    public function mapping(Request $request, MigrationSource $source, CreateMapping $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($source, $request->validate(['mapping_ref' => 'required|string|max:190', 'entity_type' => 'required|string|max:100', 'field_map' => 'required|array|min:1', 'transforms' => 'nullable|array', 'validation_rules' => 'nullable|array', 'version' => 'nullable|integer|min:1']))], 201);
    }

    public function batches(Request $request, MigrationQuery $query): mixed
    {
        return $query->batches($request->integer('team_id') ?: null, $request->integer('per_page', 25));
    }

    public function batch(Request $request, MigrationSource $source, CreateBatch $action): MigrationBatchResource
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'batch_ref' => 'required|string|max:190', 'mapping_id' => 'required|integer', 'dry_run' => 'nullable|boolean', 'rows' => 'required|array|min:1', 'rows.*.source_key' => 'required|string|max:190', 'rows.*.row_ref' => 'nullable|string|max:190', 'rows.*.payload' => 'nullable|array']);
        $mapping = MigrationMapping::query()->where('source_id', $source->id)->findOrFail($data['mapping_id']);

        return new MigrationBatchResource($action->handle($source, $mapping, $data, $data['rows']));
    }

    public function dryRun(MigrationBatch $batch, RunBatch $action): MigrationBatchResource
    {
        return new MigrationBatchResource($action->handle($batch, true));
    }

    public function resume(MigrationBatch $batch, RunBatch $action): MigrationBatchResource
    {
        return new MigrationBatchResource($action->handle($batch, false));
    }

    public function reconcile(Request $request, MigrationBatch $batch, ReconcileBatch $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($batch, $request->validate(['source_total' => 'nullable|numeric', 'destination_total' => 'nullable|numeric', 'notes' => 'nullable|string']))]);
    }

    public function counts(MigrationBatch $batch, MigrationQuery $query): array
    {
        return $query->counts($batch);
    }
}
