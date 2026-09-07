<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRulesApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\BankRules\Actions\SaveBankRule;
use Liberu\Accounting\BankRules\Actions\TestBankRule;
use Liberu\Accounting\BankRules\Models\BankRule;
use Liberu\Accounting\BankRulesApi\Http\Resources\BankRuleResource;

final class BankRuleController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', BankRule::class);

        return BankRuleResource::collection($this->scoped()->paginate(min(100, max(1, $request->integer('page.size', 25)))));
    }

    public function store(Request $request, SaveBankRule $action): BankRuleResource
    {
        Gate::authorize('create', BankRule::class);

        return new BankRuleResource($action->handle($this->validated($request) + ['team_id' => auth()->user()->current_team_id]));
    }

    public function show(string $rule): BankRuleResource
    {
        $model = $this->rule($rule);
        Gate::authorize('view', $model);

        return new BankRuleResource($model);
    }

    public function update(Request $request, string $rule, SaveBankRule $action): BankRuleResource
    {
        $model = $this->rule($rule);
        Gate::authorize('update', $model);

        return new BankRuleResource($action->handle($this->validated($request), $model));
    }

    public function destroy(string $rule): mixed
    {
        $model = $this->rule($rule);
        Gate::authorize('delete', $model);
        $model->update(['enabled' => false]);

        return response()->noContent();
    }

    public function test(Request $request, string $rule, TestBankRule $action): mixed
    {
        $model = $this->rule($rule);
        Gate::authorize('view', $model);
        $result = $action->handle($model, $request->validate(['transaction' => ['required', 'array']])['transaction']);

        return response()->json(['data' => $result]);
    }

    public function history(string $rule): mixed
    {
        $model = $this->rule($rule);
        Gate::authorize('view', $model);

        return response()->json(['data' => $model->histories()->latest('created_at')->paginate(25)]);
    }

    private function validated(Request $request): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:160'], 'priority' => ['nullable', 'integer', 'min:0'], 'enabled' => ['nullable', 'boolean'], 'conditions' => ['required', 'array'], 'actions' => ['required', 'array'], 'automation_mode' => ['nullable', 'in:disabled,suggest,automatic'], 'metadata' => ['nullable', 'array']]);
    }

    private function rule(string $id): BankRule
    {
        return $this->scoped()->findOrFail($id);
    }

    private function scoped(): mixed
    {
        return BankRule::query()->where('team_id', auth()->user()->current_team_id)->orderByDesc('priority')->orderBy('name');
    }
}
