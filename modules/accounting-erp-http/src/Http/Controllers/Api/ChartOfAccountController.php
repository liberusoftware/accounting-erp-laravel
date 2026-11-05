<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpHttp\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\ErpHttp\Http\Controllers\Controller;

class ChartOfAccountController extends Controller
{
    private const TYPES = 'asset,liability,equity,revenue,income,expense';

    public function index(Request $request): LengthAwarePaginator
    {
        return Account::where('team_id', $request->user()->current_team_id ?? -1)
            ->orderBy('account_number')
            ->paginate(25);
    }

    public function store(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        // Scope account references/uniqueness to the acting team (accounts.team_id):
        // parent_id can't point at another team's account (IDOR), and account_number
        // is unique per-tenant rather than global. -1 sentinel: a tenantless user
        // matches no rows instead of colliding with team 1 / unassigned accounts.
        $validated = $request->validate([
            'account_number' => ['required', 'integer', Rule::unique('accounts', 'account_number')->where('team_id', $teamId ?? -1)],
            'account_name' => 'required|string',
            'account_type' => 'required|in:'.self::TYPES,
            'normal_balance' => 'sometimes|in:debit,credit',
            'opening_balance' => 'sometimes|numeric',
            'description' => 'sometimes|nullable|string',
            'parent_id' => ['sometimes', 'nullable', Rule::exists('accounts', 'id')->where('team_id', $teamId ?? -1)],
            'is_active' => 'sometimes|boolean',
        ]);

        $account = new Account($validated);
        $account->team_id = $teamId; // user_id is still stamped by the model's creating hook
        $account->save();

        return response()->json($account, 201);
    }

    public function show(Request $request, Account $chartOfAccount): JsonResponse
    {
        $this->authorizeOwner($request, $chartOfAccount);

        return response()->json($chartOfAccount);
    }

    public function update(Request $request, Account $chartOfAccount): JsonResponse
    {
        $this->authorizeOwner($request, $chartOfAccount);

        $teamId = $request->user()->current_team_id;

        $validated = $request->validate([
            'account_number' => ['sometimes', 'integer', Rule::unique('accounts', 'account_number')->where('team_id', $teamId ?? -1)->ignore($chartOfAccount->id)],
            'account_name' => 'sometimes|string',
            'account_type' => 'sometimes|in:'.self::TYPES,
            'normal_balance' => 'sometimes|in:debit,credit',
            'opening_balance' => 'sometimes|numeric',
            'description' => 'sometimes|nullable|string',
            'parent_id' => ['sometimes', 'nullable', Rule::exists('accounts', 'id')->where('team_id', $teamId ?? -1)],
            'is_active' => 'sometimes|boolean',
        ]);

        $chartOfAccount->update($validated);

        return response()->json($chartOfAccount);
    }

    public function destroy(Request $request, Account $chartOfAccount): JsonResponse
    {
        $this->authorizeOwner($request, $chartOfAccount);

        $chartOfAccount->delete();

        return response()->json(['deleted' => true]);
    }

    private function authorizeOwner(Request $request, Account $account): void
    {
        // ?? -1 so a tenantless caller (null current_team_id) never matches a
        // null-team_id account — null === null would otherwise pass.
        abort_unless((int) $account->team_id === ($request->user()->current_team_id ?? -1), 403);
    }
}
