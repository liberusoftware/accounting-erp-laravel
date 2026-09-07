<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\SalesInvoicing\Actions\ApproveInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\CreateInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\FinalizeInvoice;
use Liberu\Accounting\SalesInvoicing\Actions\MarkInvoiceDelivered;
use Liberu\Accounting\SalesInvoicing\Actions\RecordDeposit;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;
use Liberu\Accounting\SalesInvoicingApi\Http\Resources\SalesInvoiceResource;

final class SalesInvoicingController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', SalesInvoice::class);

        return SalesInvoiceResource::collection(SalesInvoice::with('lines')->latest()->paginate(min($request->integer('per_page', 25), 100)));
    }

    public function show(string $invoice): SalesInvoiceResource
    {
        $model = SalesInvoice::with('lines')->findOrFail($invoice);
        Gate::authorize('view', $model);

        return new SalesInvoiceResource($model);
    }

    private function data(Request $request): array
    {
        return $request->validate(['invoice_number' => ['required', 'string', 'max:80'], 'party_id' => ['nullable', 'integer'], 'invoice_date' => ['required', 'date'], 'due_on' => ['nullable', 'date', 'after_or_equal:invoice_date'], 'currency' => ['required', 'string', 'size:3'], 'notes' => ['nullable', 'string'], 'branding' => ['nullable', 'array'], 'recurring_source' => ['nullable', 'array'], 'lines' => ['required', 'array', 'min:1'], 'lines.*.description' => ['required', 'string', 'max:255'], 'lines.*.quantity' => ['required', 'numeric', 'gt:0'], 'lines.*.unit_price' => ['required', 'numeric', 'min:0'], 'lines.*.discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'], 'lines.*.tax_rate' => ['nullable', 'numeric', 'min:0']]);
    }

    public function store(Request $request, CreateInvoice $create)
    {
        Gate::authorize('create', SalesInvoice::class);
        $data = $this->data($request);
        try {
            return (new SalesInvoiceResource($create->handle(collect($data)->except('lines')->all(), $data['lines'])))->response()->setStatusCode(201);
        } catch (InvalidInvoice $e) {
            throw ValidationException::withMessages(['lines' => $e->getMessage()]);
        }
    }

    public function approve(string $invoice, ApproveInvoice $approve): SalesInvoiceResource
    {
        $model = SalesInvoice::findOrFail($invoice);
        Gate::authorize('update', $model);
        try {
            return new SalesInvoiceResource($approve->handle($model));
        } catch (InvalidInvoice $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }
    }

    public function finalize(Request $request, string $invoice, FinalizeInvoice $finalize): SalesInvoiceResource
    {
        $model = SalesInvoice::findOrFail($invoice);
        Gate::authorize('update', $model);
        try {
            $actor = $request->user()?->getAuthIdentifier();

            return new SalesInvoiceResource($finalize->handle($model, $actor === null ? null : (string) $actor));
        } catch (InvalidInvoice $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }
    }

    public function deposit(Request $request, string $invoice, RecordDeposit $deposit): SalesInvoiceResource
    {
        $model = SalesInvoice::findOrFail($invoice);
        Gate::authorize('update', $model);
        $data = $request->validate(['amount' => ['required', 'numeric', 'gt:0'], 'reference' => ['nullable', 'string', 'max:160']]);
        try {
            return new SalesInvoiceResource($deposit->handle($model, $data));
        } catch (InvalidInvoice $e) {
            throw ValidationException::withMessages(['amount' => $e->getMessage()]);
        }
    }

    public function deliver(string $invoice, MarkInvoiceDelivered $deliver): SalesInvoiceResource
    {
        $model = SalesInvoice::findOrFail($invoice);
        Gate::authorize('update', $model);
        try {
            return new SalesInvoiceResource($deliver->handle($model));
        } catch (InvalidInvoice $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }
    }

    public function destroy(string $invoice): Response
    {
        $model = SalesInvoice::findOrFail($invoice);
        Gate::authorize('delete', $model);
        $model->delete();

        return response()->noContent();
    }
}
