<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliationApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\BankReconciliation\Actions\AddReconciliationEntry;
use Liberu\Accounting\BankReconciliation\Actions\ConfirmReconciliationEntry;
use Liberu\Accounting\BankReconciliation\Actions\CreateReconciliationSession;
use Liberu\Accounting\BankReconciliation\Actions\SignOffReconciliation;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;
use Liberu\Accounting\BankReconciliation\Queries\ReconciliationSummaryQuery;
use Liberu\Accounting\BankReconciliationApi\Http\Resources\ReconciliationResource;

final class ReconciliationController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', ReconciliationSession::class);

        return ReconciliationResource::collection($this->scoped()->latest()->paginate(min(100, max(1, $request->integer('page.size', 25)))));
    }

    public function store(Request $request, CreateReconciliationSession $action): ReconciliationResource
    {
        Gate::authorize('create', ReconciliationSession::class);

        $attributes = $request->validate([
            'bank_account_id' => ['required', 'integer'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date'],
            'opening_balance' => ['required', 'numeric'],
            'statement_balance' => ['required', 'numeric'],
            'metadata' => ['nullable', 'array'],
        ]);

        return new ReconciliationResource($action->handle(array_merge($attributes, ['team_id' => auth()->user()?->current_team_id, 'user_id' => auth()->id()])));
    }

    public function show(string $session): ReconciliationResource
    {
        $model = $this->session($session);
        Gate::authorize('view', $model);

        return new ReconciliationResource($model);
    }

    public function entry(Request $request, string $session, AddReconciliationEntry $action): mixed
    {
        $model = $this->session($session);
        Gate::authorize('update', $model);

        return $action->handle($model, $request->validate(['source_type' => ['nullable', 'string', 'max:120'], 'source_id' => ['nullable', 'string', 'max:180'], 'kind' => ['required', 'string'], 'status' => ['nullable', 'string'], 'amount' => ['required', 'numeric'], 'currency' => ['required', 'string', 'size:3'], 'confidence' => ['nullable', 'numeric', 'between:0,1'], 'description' => ['nullable', 'string'], 'metadata' => ['nullable', 'array'], 'exception_reason' => ['nullable', 'string']]));
    }

    public function confirm(string $session, string $entry, ConfirmReconciliationEntry $action): mixed
    {
        $model = $this->session($session);
        Gate::authorize('update', $model);

        return $action->handle($model->entries()->findOrFail($entry));
    }

    public function signOff(string $session, SignOffReconciliation $action): ReconciliationResource
    {
        $model = $this->session($session);
        Gate::authorize('update', $model);

        return new ReconciliationResource($action->handle($model));
    }

    public function summary(string $session, ReconciliationSummaryQuery $query): mixed
    {
        $model = $this->session($session);
        Gate::authorize('view', $model);

        return response()->json(['data' => $query->handle($model)]);
    }

    private function session(string $id): ReconciliationSession
    {
        return $this->scoped()->findOrFail($id);
    }

    private function scoped(): mixed
    {
        return ReconciliationSession::query()->when(auth()->user()?->current_team_id !== null, fn ($query): mixed => $query->where('team_id', auth()->user()->current_team_id));
    }
}
