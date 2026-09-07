<?php

declare(strict_types=1);

namespace Liberu\Accounting\PeriodsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\Periods\Actions\CreatePeriod;
use Liberu\Accounting\Periods\Actions\LockPeriod;
use Liberu\Accounting\Periods\Actions\TransitionPeriod;
use Liberu\Accounting\Periods\Actions\UnlockPeriod;
use Liberu\Accounting\Periods\Enums\PeriodState;
use Liberu\Accounting\Periods\Exceptions\InvalidPeriodTransition;
use Liberu\Accounting\Periods\Models\AccountingPeriod;
use Liberu\Accounting\PeriodsApi\Http\Requests\StoreAccountingPeriodRequest;
use Liberu\Accounting\PeriodsApi\Http\Resources\AccountingPeriodResource;

final class AccountingPeriodController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AccountingPeriod::class);

        return AccountingPeriodResource::collection(AccountingPeriod::query()->when($request->integer('book_id'), fn ($q, $id) => $q->where('book_id', $id))->latest()->paginate(min($request->integer('per_page', 25), 100)));
    }

    public function show(string $period): AccountingPeriodResource
    {
        $model = AccountingPeriod::findOrFail($period);
        Gate::authorize('view', $model);

        return new AccountingPeriodResource($model);
    }

    public function store(Request $request, CreatePeriod $create)
    {
        Gate::authorize('create', AccountingPeriod::class);
        $data = $request->validate((new StoreAccountingPeriodRequest())->rules());
        try {
            return (new AccountingPeriodResource($create->handle($data)))->response()->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages(['starts_on' => $e->getMessage()]);
        }
    }

    public function transition(Request $request, string $period, TransitionPeriod $transition): AccountingPeriodResource
    {
        $model = AccountingPeriod::findOrFail($period);
        Gate::authorize('update', $model);
        $data = $request->validate(['state' => ['required', 'in:open,soft_closed,hard_closed'], 'reason' => ['nullable', 'string', 'max:2000']]);
        try {
            return new AccountingPeriodResource($transition->handle($model, PeriodState::from($data['state']), $request->user()?->getAuthIdentifier() === null ? null : (string) $request->user()->getAuthIdentifier(), $data['reason'] ?? null));
        } catch (InvalidPeriodTransition $e) {
            throw ValidationException::withMessages(['state' => $e->getMessage()]);
        }
    }

    public function lock(Request $request, string $period, LockPeriod $lock): AccountingPeriodResource
    {
        $model = AccountingPeriod::findOrFail($period);
        Gate::authorize('update', $model);
        try {
            return new AccountingPeriodResource($lock->handle($model, (string) $request->user()->getAuthIdentifier()));
        } catch (InvalidPeriodTransition $e) {
            throw ValidationException::withMessages(['state' => $e->getMessage()]);
        }
    }

    public function unlock(Request $request, string $period, UnlockPeriod $unlock): AccountingPeriodResource
    {
        $model = AccountingPeriod::findOrFail($period);
        Gate::authorize('update', $model);
        $reason = $request->validate(['reason' => ['required', 'string', 'max:2000']])['reason'];
        try {
            return new AccountingPeriodResource($unlock->handle($model, (string) $request->user()->getAuthIdentifier(), $reason));
        } catch (InvalidPeriodTransition $e) {
            throw ValidationException::withMessages(['reason' => $e->getMessage()]);
        }
    }

    public function postingAllowed(Request $request, string $period): JsonResponse
    {
        $model = AccountingPeriod::findOrFail($period);
        Gate::authorize('view', $model);
        $date = $request->validate(['date' => ['required', 'date']])['date'];

        return response()->json(['data' => ['allowed' => $model->isPostingAllowed($date), 'date' => $date]]);
    }

    public function destroy(string $period): Response
    {
        $model = AccountingPeriod::findOrFail($period);
        Gate::authorize('update', $model);
        abort_if($model->state !== PeriodState::Open, 409, 'Only open periods may be deleted.');
        $model->delete();

        return response()->noContent();
    }
}
