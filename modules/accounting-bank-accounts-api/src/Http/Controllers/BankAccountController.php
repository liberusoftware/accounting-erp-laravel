<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\BankAccounts\Actions\CreateBankAccount;
use Liberu\Accounting\BankAccounts\Actions\SetBankAccountStatus;
use Liberu\Accounting\BankAccounts\Actions\UpdateBankAccount;
use Liberu\Accounting\BankAccounts\Enums\BankAccountStatus;
use Liberu\Accounting\BankAccounts\Models\BankAccount;
use Liberu\Accounting\BankAccounts\Queries\BankAccountBalanceQuery;
use Liberu\Accounting\BankAccountsApi\Http\Resources\BankAccountResource;

final class BankAccountController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', BankAccount::class);

        return BankAccountResource::collection(BankAccount::query()->when($request->integer('legal_entity_id'), fn ($query, $id) => $query->where('legal_entity_id', $id))->when($request->string('status')->value(), fn ($query, $status) => $query->where('status', $status))->latest()->paginate(min(100, max(1, $request->integer('page.size', 25)))));
    }

    public function store(Request $request, CreateBankAccount $action): BankAccountResource
    {
        Gate::authorize('create', BankAccount::class);

        return new BankAccountResource($action->handle($request->validate($this->rules())));
    }

    public function show(string $bankAccount): BankAccountResource
    {
        $account = $this->account($bankAccount);
        Gate::authorize('view', $account);

        return new BankAccountResource($account->load('legalEntity'));
    }

    public function update(Request $request, string $bankAccount, UpdateBankAccount $action): BankAccountResource
    {
        $account = $this->account($bankAccount);
        Gate::authorize('update', $account);

        return new BankAccountResource($action->handle($account, $request->validate(array_merge($this->rules(), ['legal_entity_id' => ['sometimes', 'integer'], 'opening_balance' => ['sometimes', 'numeric', 'min:0'], 'opening_date' => ['sometimes', 'date']]))));
    }

    public function status(Request $request, string $bankAccount, SetBankAccountStatus $action): BankAccountResource
    {
        $account = $this->account($bankAccount);
        Gate::authorize('update', $account);
        $status = BankAccountStatus::from($request->validate(['status' => ['required', 'string', 'in:active,inactive,closed']])['status']);

        return new BankAccountResource($action->handle($account, $status));
    }

    public function balances(Request $request, BankAccountBalanceQuery $query): JsonResponse
    {
        Gate::authorize('viewAny', BankAccount::class);

        return response()->json(['data' => $query->handle($request->integer('legal_entity_id') ?: null)]);
    }

    private function account(string $id): BankAccount
    {
        return BankAccount::query()->findOrFail($id);
    }

    private function rules(): array
    {
        return ['legal_entity_id' => ['required', 'integer'], 'name' => ['required', 'string', 'max:160'], 'institution_name' => ['nullable', 'string', 'max:160'], 'account_type' => ['required', 'string', 'in:bank,current,savings,credit,loan,cash'], 'currency' => ['required', 'string', 'size:3'], 'opening_balance' => ['required', 'numeric', 'min:0'], 'opening_date' => ['required', 'date'], 'account_number' => ['nullable', 'string', 'max:255'], 'routing_number' => ['nullable', 'string', 'max:255'], 'feed_reference' => ['nullable', 'string', 'max:160'], 'metadata' => ['nullable', 'array']];
    }
}
