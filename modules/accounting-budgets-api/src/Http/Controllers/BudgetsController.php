<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\Budgets\Actions\AddBudgetLine;
use Liberu\Accounting\Budgets\Actions\ApproveBudget;
use Liberu\Accounting\Budgets\Actions\CreateBudget;
use Liberu\Accounting\Budgets\Actions\ReviseBudget;
use Liberu\Accounting\Budgets\Actions\SubmitBudget;
use Liberu\Accounting\Budgets\Models\Budget;
use Liberu\Accounting\Budgets\Queries\BudgetQuery;
use Liberu\Accounting\BudgetsApi\Http\Resources\BudgetResource;

final class BudgetsController extends Controller
{
    public function index(Request $request, BudgetQuery $query): mixed
    {
        Gate::authorize('viewAny', Budget::class);

        return BudgetResource::collection($query->paginate($this->teamId($request), $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateBudget $action): BudgetResource
    {
        Gate::authorize('create', Budget::class);
        $data = $request->validate(['name' => 'required|string|max:255', 'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'currency' => 'required|string|size:3', 'notes' => 'nullable|string', 'metadata' => 'nullable|array']);

        return new BudgetResource($action->handle([...$data, 'team_id' => $this->teamId($request)]));
    }

    public function show(Request $request, Budget $budget): BudgetResource
    {
        $this->authorizeBudget($request, $budget, 'view');

        return new BudgetResource($budget->load('lines'));
    }

    public function addLine(Request $request, Budget $budget, AddBudgetLine $action): BudgetResource
    {
        $budget = $this->authorizeBudget($request, $budget, 'update');
        $data = $request->validate(['account_id' => 'required|integer', 'project_id' => 'nullable|integer', 'planned_amount' => 'required|numeric', 'dimensions' => 'nullable|array', 'phases' => 'nullable|array', 'notes' => 'nullable|string']);
        $action->handle($budget, $data);

        return new BudgetResource($budget->load('lines'));
    }

    public function submit(Request $request, Budget $budget, SubmitBudget $action): BudgetResource
    {
        $budget = $this->authorizeBudget($request, $budget, 'update');

        return new BudgetResource($action->handle($budget, (int) $request->user()->getAuthIdentifier()));
    }

    public function approve(Request $request, Budget $budget, ApproveBudget $action): BudgetResource
    {
        $budget = $this->authorizeBudget($request, $budget, 'update');

        return new BudgetResource($action->handle($budget, (int) $request->user()->getAuthIdentifier()));
    }

    public function revise(Request $request, Budget $budget, ReviseBudget $action): BudgetResource
    {
        $budget = $this->authorizeBudget($request, $budget, 'update');

        return new BudgetResource($action->handle($budget, $request->validate(['notes' => 'nullable|string', 'metadata' => 'nullable|array'])));
    }

    private function teamId(Request $request): int
    {
        $id = $request->user()?->current_team_id;
        abort_if($id === null, 403, 'A team context is required.');

        return (int) $id;
    }

    private function authorizeBudget(Request $request, Budget $budget, string $ability): Budget
    {
        $budget = Budget::query()->findOrFail((int) $request->route('budget'));
        Gate::authorize($ability, $budget);

        return $budget;
    }
}
