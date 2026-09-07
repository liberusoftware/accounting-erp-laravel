<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\AccountReconciliations\Actions\CarryForwardAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Actions\CertifyAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Actions\CreateAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Actions\PrepareAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Actions\ReviewAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Queries\AccountReconciliationQuery;
use Liberu\Accounting\AccountReconciliationsApi\Http\Resources\AccountReconciliationResource;

final class AccountReconciliationController extends Controller
{
    public function index(Request $request, AccountReconciliationQuery $query): mixed
    {
        Gate::authorize('viewAny', AccountReconciliation::class);

        return AccountReconciliationResource::collection($query->paginate($request->user()?->current_team_id, $request->string('status')->toString() ?: null, $request->integer('page.size', 25)));
    }

    public function show(AccountReconciliation $reconciliation): AccountReconciliationResource
    {
        $reconciliation = AccountReconciliation::query()->findOrFail((int) request()->route('reconciliation'));
        Gate::authorize('view', $reconciliation);

        return new AccountReconciliationResource($reconciliation);
    }

    public function store(Request $request, CreateAccountReconciliation $action): AccountReconciliationResource
    {
        Gate::authorize('create', AccountReconciliation::class);
        $data = $request->validate(['book_id' => ['required', 'integer'], 'account_id' => ['required', 'integer'], 'period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after_or_equal:period_start'], 'template' => ['nullable', 'array']]);

        return new AccountReconciliationResource($action->handle([...$data, 'team_id' => $request->user()->current_team_id]));
    }

    public function prepare(Request $request, AccountReconciliation $reconciliation, PrepareAccountReconciliation $action): AccountReconciliationResource
    {
        $reconciliation = AccountReconciliation::query()->findOrFail((int) $request->route('reconciliation'));
        $this->authorizeRecord($reconciliation);
        $data = $request->validate(['source_balance' => ['required', 'array'], 'source_balance.amount' => ['required', 'numeric'], 'supporting_items' => ['array']]);

        return new AccountReconciliationResource($action->handle($reconciliation, ['user_id' => $request->user()->getAuthIdentifier()], $data['source_balance'], $data['supporting_items'] ?? []));
    }

    public function review(Request $request, AccountReconciliation $reconciliation, ReviewAccountReconciliation $action): AccountReconciliationResource
    {
        $reconciliation = AccountReconciliation::query()->findOrFail((int) $request->route('reconciliation'));
        $this->authorizeRecord($reconciliation);

        return new AccountReconciliationResource($action->handle($reconciliation, ['user_id' => $request->user()->getAuthIdentifier(), 'comment' => $request->string('comment')->toString()]));
    }

    public function certify(Request $request, AccountReconciliation $reconciliation, CertifyAccountReconciliation $action): AccountReconciliationResource
    {
        $reconciliation = AccountReconciliation::query()->findOrFail((int) $request->route('reconciliation'));
        $this->authorizeRecord($reconciliation);

        return new AccountReconciliationResource($action->handle($reconciliation, ['user_id' => $request->user()->getAuthIdentifier(), 'attestation' => $request->validate(['attestation' => ['required', 'string', 'max:1000']])['attestation']]));
    }

    public function carryForward(Request $request, AccountReconciliation $reconciliation, CarryForwardAccountReconciliation $action): AccountReconciliationResource
    {
        $reconciliation = AccountReconciliation::query()->findOrFail((int) $request->route('reconciliation'));
        $this->authorizeRecord($reconciliation);

        return new AccountReconciliationResource($action->handle($reconciliation, $request->validate(['period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after_or_equal:period_start'], 'amount' => ['nullable', 'numeric']])));
    }

    private function authorizeRecord(AccountReconciliation $reconciliation): void
    {
        Gate::authorize('update', $reconciliation);
    }
}
