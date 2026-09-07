<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeedsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\BankFeeds\Actions\CreateBankFeedConnection;
use Liberu\Accounting\BankFeeds\Actions\ImportBankFeedTransactions;
use Liberu\Accounting\BankFeeds\Actions\MapBankFeedAccount;
use Liberu\Accounting\BankFeeds\Actions\UpsertBankFeedInstitution;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;
use Liberu\Accounting\BankFeedsApi\Http\Resources\BankFeedResource;

final class BankFeedController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', BankFeedConnection::class);

        return BankFeedResource::collection($this->scopedConnections()->with('institution')->latest()->paginate(min(100, max(1, $request->integer('page.size', 25)))));
    }

    public function show(string $connection): BankFeedResource
    {
        $model = $this->connection($connection);
        Gate::authorize('view', $model);

        return new BankFeedResource($model->load('institution'));
    }

    public function institution(Request $request, UpsertBankFeedInstitution $action): mixed
    {
        Gate::authorize('create', BankFeedConnection::class);

        return $action->handle($request->validate([
            'provider' => ['required', 'string', 'max:80'], 'external_id' => ['required', 'string', 'max:180'],
            'name' => ['required', 'string', 'max:160'], 'country' => ['nullable', 'string', 'size:2'],
            'logo_url' => ['nullable', 'url'], 'metadata' => ['nullable', 'array'],
        ]));
    }

    public function store(Request $request, CreateBankFeedConnection $action): BankFeedResource
    {
        Gate::authorize('create', BankFeedConnection::class);

        return new BankFeedResource($action->handle(array_merge($request->validate([
            'institution_id' => ['required', 'integer'],
            'provider' => ['required', 'string', 'max:80'], 'name' => ['required', 'string', 'max:160'],
            'external_reference' => ['required', 'string', 'max:180'], 'access_token' => ['required', 'string'],
            'credentials' => ['nullable', 'array'], 'metadata' => ['nullable', 'array'],
        ]), ['team_id' => auth()->user()?->current_team_id, 'user_id' => auth()->id()])));
    }

    public function map(Request $request, string $connection, MapBankFeedAccount $action): mixed
    {
        $model = $this->connection($connection);
        Gate::authorize('update', $model);

        return $action->handle($model, $request->validate([
            'bank_account_id' => ['required', 'integer'], 'external_account_id' => ['required', 'string', 'max:180'],
            'name' => ['required', 'string', 'max:160'], 'currency' => ['required', 'string', 'size:3'], 'metadata' => ['nullable', 'array'],
        ]));
    }

    public function import(Request $request, string $connection, ImportBankFeedTransactions $action): mixed
    {
        $model = $this->connection($connection);
        Gate::authorize('update', $model);

        return response()->json(['data' => $action->handle($model, $request->validate([
            'added' => ['array'], 'modified' => ['array'], 'removed' => ['array'], 'next_cursor' => ['nullable', 'string'],
        ]))], 202);
    }

    private function connection(string $id): BankFeedConnection
    {
        return $this->scopedConnections()->findOrFail($id);
    }

    private function scopedConnections(): mixed
    {
        $teamId = auth()->user()?->current_team_id;

        return BankFeedConnection::query()->when($teamId !== null, fn ($query): mixed => $query->where('team_id', $teamId));
    }
}
