<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ProductAndServiceItems\Actions\SaveAccountingItem;
use Liberu\Accounting\ProductAndServiceItems\Models\AccountingItem;
use Liberu\Accounting\ProductAndServiceItems\Queries\FindAccountingItems;
use Liberu\Accounting\ProductAndServiceItemsApi\Http\Resources\AccountingItemResource;

final class ProductAndServiceItemsController extends Controller
{
    public function index(Request $request, FindAccountingItems $query): AnonymousResourceCollection
    {
        return AccountingItemResource::collection($query->search($request->string('search')->toString())->where('team_id', $this->teamId($request))->paginate(min(max($request->integer('per_page', 25), 1), 100)));
    }

    public function store(Request $request, SaveAccountingItem $action): JsonResponse
    {
        $data = $request->validate(['code' => 'required|string|max:100', 'name' => 'required|string|max:255', 'kind' => 'required|in:item,service', 'purchase_description' => 'nullable|string', 'sales_description' => 'nullable|string', 'sales_account_ref' => 'nullable|string', 'purchase_account_ref' => 'nullable|string', 'tax_default_ref' => 'nullable|string', 'unit' => 'nullable|string|max:32', 'purchase_price' => 'nullable|numeric|min:0', 'sales_price' => 'nullable|numeric|min:0', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'status' => 'nullable|in:active,inactive,archived', 'ecommerce_refs' => 'nullable|array', 'metadata' => 'nullable|array']);

        return (new AccountingItemResource($action->handle([...$data, 'team_id' => $this->teamId($request)])))->response()->setStatusCode(201);
    }

    public function show(Request $request, AccountingItem $accountingItem): AccountingItemResource
    {
        abort_unless((int) $accountingItem->team_id === $this->teamId($request), 404);

        return new AccountingItemResource($accountingItem);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}
