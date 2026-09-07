<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCardsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CorporateCards\Actions\CodeCardTransaction;
use Liberu\Accounting\CorporateCards\Actions\CreateCardAccount;
use Liberu\Accounting\CorporateCards\Actions\RecordCardTransaction;
use Liberu\Accounting\CorporateCards\Models\CardAccount;
use Liberu\Accounting\CorporateCards\Models\CardTransaction;
use Liberu\Accounting\CorporateCards\Queries\CorporateCardQuery;

final class CorporateCardsController extends Controller
{
    public function index(CorporateCardQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCardAccount $action): JsonResponse
    {
        $data = $request->validate(['card_ref' => ['required', 'string', 'max:160'], 'holder_ref' => ['required', 'string', 'max:160'], 'provider_ref' => ['nullable', 'string', 'max:160'], 'currency' => ['required', 'string', 'size:3'], 'limit_amount' => ['required', 'numeric', 'gt:0'], 'controls' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function transaction(Request $request, string $account, RecordCardTransaction $action): JsonResponse
    {
        $model = CardAccount::query()->where('team_id', $this->teamId())->findOrFail($account);
        $data = $request->validate(['transaction_ref' => ['required', 'string', 'max:160'], 'transaction_date' => ['required', 'date'], 'amount' => ['required', 'numeric', 'gt:0'], 'currency' => ['required', 'string', 'size:3'], 'merchant_ref' => ['nullable', 'string', 'max:160'], 'feed_ref' => ['nullable', 'string', 'max:160'], 'metadata' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle($model, $data)], 201);
    }

    public function code(Request $request, string $transaction, CodeCardTransaction $action): JsonResponse
    {
        $model = CardTransaction::query()->where('team_id', $this->teamId())->findOrFail($transaction);
        $data = $request->validate(['category_ref' => ['required', 'string', 'max:160'], 'receipt_ref' => ['nullable', 'string', 'max:160']]);

        return response()->json(['data' => $action->handle($model, $data['category_ref'], $data['receipt_ref'] ?? null)]);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}
