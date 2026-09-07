<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotesApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\EstimatesAndQuotes\Actions\AddEstimateItem;
use Liberu\Accounting\EstimatesAndQuotes\Actions\ConvertEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Actions\CreateEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Actions\DecideEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Actions\ExpireEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Actions\SendEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;
use Liberu\Accounting\EstimatesAndQuotes\Queries\EstimateQuery;
use Liberu\Accounting\EstimatesAndQuotesApi\Http\Resources\EstimateResource;

final class EstimatesController extends Controller
{
    public function __construct(private readonly EstimateQuery $query) {}

    public function index(Request $r): mixed
    {
        $s = $r->filled('status') ? EstimateStatus::tryFrom($r->string('status')->toString()) : null;
        abort_if($r->filled('status') && $s === null, 422, 'Unknown estimate status.');

        return EstimateResource::collection($this->query->paginate($r->integer('legal_entity_id') ?: null, $s, $r->integer('per_page', 25)));
    }

    public function store(Request $r, CreateEstimate $a): EstimateResource
    {
        $d = $r->validate(['legal_entity_id' => 'required|integer', 'customer_ref' => 'required|string|max:255', 'quote_ref' => 'required|string|max:100', 'name' => 'required|string|max:255', 'currency' => 'required|string|size:3', 'issue_date' => 'required|date', 'expires_on' => 'nullable|date|after_or_equal:issue_date', 'terms' => 'nullable|string', 'brand' => 'nullable|array', 'metadata' => 'nullable|array']);

        return new EstimateResource($a->handle($d));
    }

    public function show(Estimate $e): EstimateResource
    {
        return new EstimateResource($e->load(['items', 'versions', 'history']));
    }

    public function item(Request $r, Estimate $e, AddEstimateItem $a): EstimateResource
    {
        $d = $r->validate(['item_ref' => 'nullable|string|max:100', 'description' => 'required|string|max:5000', 'quantity' => 'required|numeric|gt:0', 'unit_price' => 'required|numeric|min:0', 'tax_rate' => 'nullable|numeric|min:0', 'metadata' => 'nullable|array']);
        $a->handle($e, $d);

        return new EstimateResource($e->load('items'));
    }

    public function send(Request $r, Estimate $e, SendEstimate $a): EstimateResource
    {
        return new EstimateResource($a->handle($e, (string) $r->user()->getAuthIdentifier()));
    }

    public function decide(Request $r, Estimate $e, DecideEstimate $a): EstimateResource
    {
        $d = $r->validate(['accepted' => 'required|boolean', 'reason' => 'nullable|string|max:5000']);

        return new EstimateResource($a->handle($e, (bool) $d['accepted'], $d['reason'] ?? null, (string) $r->user()->getAuthIdentifier()));
    }

    public function expire(Request $r, Estimate $e, ExpireEstimate $a): EstimateResource
    {
        return new EstimateResource($a->handle($e, (string) $r->user()->getAuthIdentifier()));
    }

    public function convert(Request $r, Estimate $e, ConvertEstimate $a): EstimateResource
    {
        $d = $r->validate(['converted_ref' => 'required|string|max:255']);

        return new EstimateResource($a->handle($e, $d['converted_ref'], (string) $r->user()->getAuthIdentifier()));
    }
}
