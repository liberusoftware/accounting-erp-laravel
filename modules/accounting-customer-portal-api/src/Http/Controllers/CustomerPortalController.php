<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortalApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CustomerPortal\Actions\CreateCustomerPortalRecord;
use Liberu\Accounting\CustomerPortal\Actions\PublishCustomerPortalRecord;
use Liberu\Accounting\CustomerPortal\Models\CustomerPortalRecord;
use Liberu\Accounting\CustomerPortal\Queries\CustomerPortalQuery;

final class CustomerPortalController extends Controller
{
    public function index(CustomerPortalQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCustomerPortalRecord $action): JsonResponse
    {
        $data = $request->validate(['customer_id' => ['required', 'string', 'max:160'], 'type' => ['required', 'in:estimate,invoice,credit,statement,payment_link,payment,dispute,document,preference'], 'reference' => ['required', 'string', 'max:160'], 'currency' => ['nullable', 'string', 'size:3'], 'amount' => ['nullable', 'numeric', 'gte:0'], 'payload' => ['nullable', 'array'], 'metadata' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function publish(Request $request, string $record, PublishCustomerPortalRecord $action): JsonResponse
    {
        $model = CustomerPortalRecord::query()->where('team_id', $this->teamId())->findOrFail($record);

        return response()->json(['data' => $action->handle($model)]);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}
