<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\SupplierBills\Actions\AddSupplierBillCredit;
use Liberu\Accounting\SupplierBills\Actions\ApproveSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\AttachSupplierBillDocument;
use Liberu\Accounting\SupplierBills\Actions\CreateSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\MatchSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\PostSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\RejectSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\UpdateSupplierBill;
use Liberu\Accounting\SupplierBills\Actions\VoidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;
use Liberu\Accounting\SupplierBills\Queries\DuplicateSupplierBillQuery;
use Liberu\Accounting\SupplierBills\Queries\SupplierBillAgingQuery;
use Liberu\Accounting\SupplierBillsApi\Http\Resources\SupplierBillResource;

final class SupplierBillController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', SupplierBill::class);

        return SupplierBillResource::collection(SupplierBill::query()->with('party')->when($request->integer('party_id'), fn ($query, $id) => $query->where('party_id', $id))->when($request->string('status')->value(), fn ($query, $status) => $query->where('status', $status))->latest()->paginate(min(100, max(1, $request->integer('page.size', 25)))));
    }

    public function show(string $supplierBill): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('view', $supplierBill);

        return new SupplierBillResource($supplierBill->load('lines', 'credits', 'documents', 'matches', 'party'));
    }

    public function store(Request $request, CreateSupplierBill $action): SupplierBillResource
    {
        Gate::authorize('create', SupplierBill::class);

        $data = $request->validate($this->rules());
        $lines = $data['lines'];
        unset($data['lines']);

        return new SupplierBillResource($action->handle($data, $lines));
    }

    public function update(Request $request, string $supplierBill, UpdateSupplierBill $action): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('update', $supplierBill);

        $data = $request->validate(array_merge($this->rules(), ['bill_number' => ['sometimes', 'string', 'max:80']]));
        $lines = $data['lines'];
        unset($data['lines']);

        return new SupplierBillResource($action->handle($supplierBill, $data, $lines));
    }

    public function approve(string $supplierBill, ApproveSupplierBill $action): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('update', $supplierBill);

        return new SupplierBillResource($action->handle($supplierBill, auth()->id()));
    }

    public function post(string $supplierBill, PostSupplierBill $action): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('update', $supplierBill);

        return new SupplierBillResource($action->handle($supplierBill));
    }

    public function reject(Request $request, string $supplierBill, RejectSupplierBill $action): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('update', $supplierBill);

        return new SupplierBillResource($action->handle($supplierBill, $request->validate(['reason' => ['required', 'string', 'max:2000']])['reason']));
    }

    public function void(Request $request, string $supplierBill, VoidSupplierBill $action): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('update', $supplierBill);

        return new SupplierBillResource($action->handle($supplierBill, $request->validate(['reason' => ['required', 'string', 'max:2000']])['reason']));
    }

    public function credit(Request $request, string $supplierBill, AddSupplierBillCredit $action): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('update', $supplierBill);

        return new SupplierBillResource($action->handle($supplierBill, $request->validate(['amount' => ['required', 'numeric', 'gt:0'], 'currency' => ['nullable', 'string', 'size:3'], 'reason' => ['required', 'string', 'max:255'], 'reference' => ['nullable', 'string', 'max:128'], 'metadata' => ['nullable', 'array']])));
    }

    public function match(Request $request, string $supplierBill, MatchSupplierBill $action): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('update', $supplierBill);

        return new SupplierBillResource($action->handle($supplierBill, $request->validate(['match_type' => ['required', 'string', 'max:32'], 'matched_type' => ['required', 'string', 'max:160'], 'matched_id' => ['required', 'string', 'max:160'], 'quantity' => ['nullable', 'numeric', 'gt:0'], 'amount' => ['nullable', 'numeric', 'gt:0'], 'metadata' => ['nullable', 'array']])));
    }

    public function document(Request $request, string $supplierBill, AttachSupplierBillDocument $action): SupplierBillResource
    {
        $supplierBill = $this->bill($supplierBill);
        Gate::authorize('update', $supplierBill);

        $data = $request->validate(['path' => ['required', 'string', 'max:1024'], 'original_name' => ['required', 'string', 'max:255'], 'mime_type' => ['required', 'string', 'max:128'], 'sha256' => ['required', 'string', 'size:64'], 'metadata' => ['nullable', 'array']]);
        $action->handle($supplierBill, $data);

        return new SupplierBillResource($supplierBill->refresh()->load('documents'));
    }

    public function duplicates(Request $request, DuplicateSupplierBillQuery $query): JsonResponse
    {
        Gate::authorize('viewAny', SupplierBill::class);

        $data = $request->validate(['party_id' => ['required', 'integer'], 'bill_number' => ['required', 'string', 'max:80']]);

        return response()->json(['data' => SupplierBillResource::collection($query->handle((int) $data['party_id'], $data['bill_number']))]);
    }

    public function aging(Request $request, SupplierBillAgingQuery $query): JsonResponse
    {
        Gate::authorize('viewAny', SupplierBill::class);

        return response()->json(['data' => $query->handle($request->integer('party_id') ?: null, $request->date('as_of'))]);
    }

    private function rules(): array
    {
        return ['party_id' => ['required', 'integer'], 'bill_number' => ['sometimes', 'string', 'max:80'], 'bill_date' => ['required', 'date'], 'due_on' => ['nullable', 'date'], 'currency' => ['required', 'string', 'size:3'], 'capture_source' => ['nullable', 'string', 'max:64'], 'purchase_order_reference' => ['nullable', 'string', 'max:128'], 'reference_number' => ['nullable', 'string', 'max:128'], 'notes' => ['nullable', 'string'], 'recurring' => ['nullable', 'boolean'], 'recurrence_frequency' => ['nullable', 'string', 'max:32'], 'recurrence_start' => ['nullable', 'date'], 'recurrence_end' => ['nullable', 'date', 'after_or_equal:recurrence_start'], 'external_ids' => ['nullable', 'array'], 'metadata' => ['nullable', 'array'], 'lines' => ['required', 'array', 'min:1'], 'lines.*.account_code' => ['nullable', 'string', 'max:64'], 'lines.*.description' => ['required', 'string', 'max:255'], 'lines.*.quantity' => ['required', 'numeric', 'gt:0'], 'lines.*.unit_price' => ['required', 'numeric', 'gte:0'], 'lines.*.discount_rate' => ['nullable', 'numeric', 'between:0,100'], 'lines.*.tax_rate' => ['nullable', 'numeric', 'gte:0'], 'lines.*.metadata' => ['nullable', 'array']];
    }

    private function bill(string $id): SupplierBill
    {
        return SupplierBill::query()->findOrFail($id);
    }
}
