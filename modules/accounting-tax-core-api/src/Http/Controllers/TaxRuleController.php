<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\TaxCore\Actions\ActivateTaxRule;
use Liberu\Accounting\TaxCore\Actions\ArchiveTaxRule;
use Liberu\Accounting\TaxCore\Actions\CreateTaxRule;
use Liberu\Accounting\TaxCore\Actions\UpdateTaxRule;
use Liberu\Accounting\TaxCore\Models\TaxRule;
use Liberu\Accounting\TaxCoreApi\Http\Resources\TaxRuleResource;

final class TaxRuleController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', TaxRule::class);

        return TaxRuleResource::collection(TaxRule::query()->when($request->string('status')->value(), fn ($q, $status) => $q->where('status', $status))->orderBy('code')->paginate(min(max($request->integer('per_page', 25), 1), 100)));
    }

    public function show(TaxRule $taxRule): TaxRuleResource
    {
        Gate::authorize('view', $taxRule);

        return new TaxRuleResource($taxRule);
    }

    public function store(Request $request, CreateTaxRule $action): mixed
    {
        Gate::authorize('create', TaxRule::class);
        $data = $request->validate(['code' => 'required|string|max:64', 'name' => 'required|string|max:255', 'tax_type' => 'required|string|max:64', 'jurisdiction_code' => 'nullable|string|max:32', 'rate' => 'numeric|min:0|max:100', 'treatment' => 'nullable|string', 'effective_from' => 'required|date', 'effective_until' => 'nullable|date|after_or_equal:effective_from', 'status' => 'nullable|string', 'exemption_code' => 'nullable|string', 'control_account_code' => 'nullable|string', 'rounding_method' => 'nullable|string', 'rounding_scale' => 'nullable|integer|min:0|max:6']);

        return (new TaxRuleResource($action->handle($data)))->response()->setStatusCode(201);
    }

    public function update(Request $request, TaxRule $taxRule, UpdateTaxRule $action): TaxRuleResource
    {
        Gate::authorize('update', $taxRule);

        return new TaxRuleResource($action->handle($taxRule, $request->validate(['name' => 'sometimes|string|max:255', 'rate' => 'sometimes|numeric|min:0|max:100', 'effective_until' => 'nullable|date', 'exemption_code' => 'nullable|string', 'control_account_code' => 'nullable|string', 'rounding_method' => 'sometimes|string', 'rounding_scale' => 'sometimes|integer|min:0|max:6'])));
    }

    public function activate(TaxRule $taxRule, ActivateTaxRule $action): TaxRuleResource
    {
        Gate::authorize('update', $taxRule);

        return new TaxRuleResource($action->handle($taxRule));
    }

    public function archive(TaxRule $taxRule, ArchiveTaxRule $action): TaxRuleResource
    {
        Gate::authorize('delete', $taxRule);

        return new TaxRuleResource($action->handle($taxRule));
    }
}
