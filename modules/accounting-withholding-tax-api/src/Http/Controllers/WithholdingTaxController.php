<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTaxApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\WithholdingTax\Actions\CalculateWithholdingTax;
use Liberu\Accounting\WithholdingTax\Actions\CreateWithholdingTaxRule;
use Liberu\Accounting\WithholdingTax\Actions\RemitWithholdingTax;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxDeduction;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxLiability;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxRule;

final class WithholdingTaxController extends Controller
{
    public function rules(Request $request): mixed
    {
        return WithholdingTaxRule::query()->where('team_id', $this->teamId($request))->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function deductions(Request $request): mixed
    {
        return WithholdingTaxDeduction::query()->where('team_id', $this->teamId($request))->with('liability')->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function createRule(Request $request, CreateWithholdingTaxRule $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['code' => 'required|string|max:64', 'name' => 'required|string|max:255', 'jurisdiction' => 'required|string|max:64', 'rate' => 'required|numeric|min:0|max:100', 'threshold' => 'nullable|numeric|min:0', 'effective_from' => 'required|date', 'effective_until' => 'nullable|date|after_or_equal:effective_from', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function calculate(Request $request, WithholdingTaxRule $rule, CalculateWithholdingTax $action): mixed
    {
        abort_unless((int) $rule->team_id === $this->teamId($request), 404);

        return response()->json($action->handle($rule, $request->validate(['party_type' => 'required|string|max:64', 'party_id' => 'required|string|max:160', 'source_ref' => 'required|string|max:160', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'gross_amount' => 'required|numeric|min:0', 'metadata' => 'nullable|array'])), 201);
    }

    public function remit(Request $request, WithholdingTaxLiability $liability, RemitWithholdingTax $action): mixed
    {
        abort_unless((int) $liability->team_id === $this->teamId($request), 404);

        return response()->json($action->handle($liability, $request->validate(['amount' => 'required|numeric|min:0', 'remitted_on' => 'required|date', 'reference' => 'required|string|max:160', 'metadata' => 'nullable|array'])), 201);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}
