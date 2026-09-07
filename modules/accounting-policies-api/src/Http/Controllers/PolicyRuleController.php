<?php

namespace Liberu\Accounting\PoliciesApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\Policies\Actions\SavePolicyRule;
use Liberu\Accounting\Policies\Exceptions\InvalidPolicyRule;
use Liberu\Accounting\Policies\Models\PolicyRule;
use Liberu\Accounting\Policies\Queries\PolicyRuleForDate;
use Liberu\Accounting\PoliciesApi\Http\Resources\PolicyRuleResource;

final class PolicyRuleController extends Controller
{
    private function rules(bool $partial = false): array
    {
        return ['book_id' => [$partial ? 'sometimes' : 'required', 'integer', 'exists:accounting_books,id'], 'category' => [$partial ? 'sometimes' : 'required', 'in:recognition,capitalization,depreciation,fx,tax,rounding,write_off,materiality,approval'], 'key' => [$partial ? 'sometimes' : 'required', 'string', 'max:100'], 'value' => [$partial ? 'sometimes' : 'required', 'array'], 'effective_from' => [$partial ? 'sometimes' : 'required', 'date'], 'effective_until' => ['nullable', 'date'], 'is_active' => ['sometimes', 'boolean'], 'approved_by' => ['nullable', 'string', 'max:191'], 'approved_at' => ['nullable', 'date'], 'metadata' => ['nullable', 'array']];
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', PolicyRule::class);

        return PolicyRuleResource::collection(PolicyRule::query()->when($request->integer('book_id'), fn ($q, $id) => $q->where('book_id', $id))->when($request->string('category')->toString(), fn ($q, $v) => $q->where('category', $v))->latest('effective_from')->paginate(min($request->integer('per_page', 25), 100)));
    }

    public function show(string $rule): PolicyRuleResource
    {
        $m = PolicyRule::findOrFail($rule);
        Gate::authorize('view', $m);

        return new PolicyRuleResource($m);
    }

    public function store(Request $request, SavePolicyRule $save)
    {
        Gate::authorize('create', PolicyRule::class);
        $data = $request->validate($this->rules());
        try {
            return (new PolicyRuleResource($save->handle(null, $data)))->response()->setStatusCode(201);
        } catch (InvalidPolicyRule $e) {
            throw ValidationException::withMessages(['effective_from' => $e->getMessage()]);
        }
    }

    public function update(Request $request, string $rule, SavePolicyRule $save): PolicyRuleResource
    {
        $m = PolicyRule::findOrFail($rule);
        Gate::authorize('update', $m);
        $data = $request->validate($this->rules(true) + []);
        $data += ['book_id' => $m->book_id, 'category' => $m->category->value, 'key' => $m->key, 'effective_from' => $m->effective_from->toDateString()];
        try {
            return new PolicyRuleResource($save->handle($m, $data));
        } catch (InvalidPolicyRule $e) {
            throw ValidationException::withMessages(['effective_from' => $e->getMessage()]);
        }
    }

    public function resolve(Request $request, PolicyRuleForDate $resolve)
    {
        $data = $request->validate(['book_id' => ['required', 'integer'], 'category' => ['required', 'in:recognition,capitalization,depreciation,fx,tax,rounding,write_off,materiality,approval'], 'key' => ['required', 'string'], 'date' => ['required', 'date']]);
        Gate::authorize('viewAny', PolicyRule::class);
        $m = $resolve->handle($data['book_id'], $data['category'], $data['key'], $data['date']);

        return $m === null ? response()->json(['data' => null]) : new PolicyRuleResource($m);
    }

    public function destroy(string $rule): Response
    {
        $m = PolicyRule::findOrFail($rule);
        Gate::authorize('delete', $m);
        $m->delete();

        return response()->noContent();
    }
}
