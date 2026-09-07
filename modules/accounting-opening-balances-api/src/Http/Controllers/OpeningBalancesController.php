<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalancesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\OpeningBalances\Actions\ApproveOpeningBalances;
use Liberu\Accounting\OpeningBalances\Actions\CreateOpeningBalanceBatch;
use Liberu\Accounting\OpeningBalances\Actions\ReconcileOpeningBalances;
use Liberu\Accounting\OpeningBalances\Actions\ValidateOpeningBalances;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;
use Liberu\Accounting\OpeningBalances\Queries\OpeningBalanceQuery;
use Liberu\Accounting\OpeningBalancesApi\Http\Resources\OpeningBalanceResource;

final class OpeningBalancesController extends Controller
{
    public function index(Request $request, OpeningBalanceQuery $query): mixed
    {
        return OpeningBalanceResource::collection($query->paginate($request->integer('team_id') ?: null, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateOpeningBalanceBatch $action): JsonResponse
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'batch_ref' => 'required|string|max:190', 'migration_date' => 'required|date', 'currency' => 'nullable|string|size:3', 'idempotency_key' => 'nullable|string|max:190', 'entries' => 'required|array|min:1', 'entries.*.balance_type' => 'required|in:account,customer,supplier,bank,item', 'entries.*.reference_id' => 'required|string|max:190', 'entries.*.reference_type' => 'nullable|string|max:160', 'entries.*.document_ref' => 'nullable|string|max:190', 'entries.*.debit_amount' => 'nullable|numeric|min:0', 'entries.*.credit_amount' => 'nullable|numeric|min:0', 'entries.*.currency' => 'nullable|string|size:3']);
        $run = $action->handle($data, $data['entries']);

        return (new OpeningBalanceResource($run))->response()->setStatusCode(201);
    }

    public function show(OpeningBalanceBatch $openingBalance): OpeningBalanceResource
    {
        return new OpeningBalanceResource($openingBalance->load('entries', 'reconciliations'));
    }

    public function validateBatch(OpeningBalanceBatch $openingBalance, ValidateOpeningBalances $action): OpeningBalanceResource
    {
        return new OpeningBalanceResource($action->handle($openingBalance));
    }

    public function approve(Request $request, OpeningBalanceBatch $openingBalance, ApproveOpeningBalances $action): OpeningBalanceResource
    {
        return new OpeningBalanceResource($action->handle($openingBalance, (int) $request->user()->getAuthIdentifier()));
    }

    public function reconcile(Request $request, OpeningBalanceBatch $openingBalance, ReconcileOpeningBalances $action): OpeningBalanceResource
    {
        $data = $request->validate(['actuals' => 'required|array|min:1', 'actuals.*.entry_id' => 'required|integer', 'actuals.*.actual_amount' => 'required|numeric', 'actuals.*.external_ref' => 'nullable|string|max:190', 'actuals.*.notes' => 'nullable|string']);

        return new OpeningBalanceResource($action->handle($openingBalance, $data['actuals']));
    }
}
