<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\EInvoicing\Actions\ArchiveEInvoice;
use Liberu\Accounting\EInvoicing\Actions\CreateEInvoice;
use Liberu\Accounting\EInvoicing\Actions\ReconcileEInvoice;
use Liberu\Accounting\EInvoicing\Actions\RecordEInvoiceReceipt;
use Liberu\Accounting\EInvoicing\Actions\SignEInvoice;
use Liberu\Accounting\EInvoicing\Actions\SubmitEInvoice;
use Liberu\Accounting\EInvoicing\Actions\ValidateEInvoice;
use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;
use Liberu\Accounting\EInvoicing\Queries\EInvoiceQuery;
use Liberu\Accounting\EInvoicingApi\Http\Resources\EInvoiceResource;

final class EInvoicingController extends Controller
{
    public function __construct(private readonly EInvoiceQuery $query) {}

    public function index(Request $r): mixed
    {
        $s = $r->filled('status') ? DocumentStatus::tryFrom($r->string('status')->toString()) : null;
        abort_if($r->filled('status') && $s === null, 422, 'Unknown e-invoice status.');

        return EInvoiceResource::collection($this->query->paginate($r->integer('legal_entity_id') ?: null, $s, $r->integer('per_page', 25)));
    }

    public function store(Request $r, CreateEInvoice $a): EInvoiceResource
    {
        $d = $r->validate(['legal_entity_id' => 'required|integer', 'document_ref' => 'required|string|max:100', 'document_type' => 'required|in:invoice,credit', 'format' => 'required|in:ubl,factur-x,peppol', 'tax_id' => 'required|string|max:100', 'counterparty_ref' => 'required|string|max:255', 'currency' => 'required|string|size:3', 'payload' => 'required|array', 'metadata' => 'nullable|array']);

        return new EInvoiceResource($a->handle($d));
    }

    public function show(EInvoiceDocument $d): EInvoiceResource
    {
        return new EInvoiceResource($d->load('events'));
    }

    public function validateDocument(Request $r, EInvoiceDocument $d, ValidateEInvoice $a): EInvoiceResource
    {
        return new EInvoiceResource($a->handle($d, (string) $r->user()->getAuthIdentifier()));
    }

    public function sign(Request $r, EInvoiceDocument $d, SignEInvoice $a): EInvoiceResource
    {
        $v = $r->validate(['signature' => 'required|string']);

        return new EInvoiceResource($a->handle($d, $v['signature'], (string) $r->user()->getAuthIdentifier()));
    }

    public function submit(Request $r, EInvoiceDocument $d, SubmitEInvoice $a): EInvoiceResource
    {
        $v = $r->validate(['provider_ref' => 'required|string|max:255']);

        return new EInvoiceResource($a->handle($d, $v['provider_ref'], (string) $r->user()->getAuthIdentifier()));
    }

    public function receipt(Request $r, EInvoiceDocument $d, RecordEInvoiceReceipt $a): EInvoiceResource
    {
        $v = $r->validate(['accepted' => 'required|boolean', 'message' => 'nullable|string|max:5000']);

        return new EInvoiceResource($a->handle($d, (bool) $v['accepted'], $v['message'] ?? null, (string) $r->user()->getAuthIdentifier()));
    }

    public function reconcile(Request $r, EInvoiceDocument $d, ReconcileEInvoice $a): EInvoiceResource
    {
        return new EInvoiceResource($a->handle($d, (string) $r->user()->getAuthIdentifier()));
    }

    public function archive(Request $r, EInvoiceDocument $d, ArchiveEInvoice $a): EInvoiceResource
    {
        return new EInvoiceResource($a->handle($d, (string) $r->user()->getAuthIdentifier()));
    }
}
