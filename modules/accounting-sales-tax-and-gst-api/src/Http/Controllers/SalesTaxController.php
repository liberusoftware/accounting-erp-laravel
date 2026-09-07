<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGstApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\SalesTaxAndGst\Actions\ActivateSalesTaxRecord;
use Liberu\Accounting\SalesTaxAndGst\Actions\CloseSalesTaxRecord;
use Liberu\Accounting\SalesTaxAndGst\Actions\CreateSalesTaxRecord;
use Liberu\Accounting\SalesTaxAndGst\Models\SalesTaxRecord;
use Liberu\Accounting\SalesTaxAndGst\Queries\SalesTaxRecordQuery;
use Liberu\Accounting\SalesTaxAndGstApi\Http\Resources\SalesTaxResource;

final class SalesTaxController extends Controller
{
    public function index(Request $request, SalesTaxRecordQuery $query): mixed
    {
        return SalesTaxResource::collection($query->paginate($request->string('context_id')->value() ?: null, $request->string('type')->value() ?: null, $request->string('status')->value() ?: null, $request->integer('per_page', 25)));
    }

    public function show(SalesTaxRecord $record): SalesTaxResource
    {
        return new SalesTaxResource($record);
    }

    public function store(Request $request, CreateSalesTaxRecord $action): SalesTaxResource
    {
        $data = $request->validate(['context_id' => 'required|string|max:160', 'type' => 'required|string', 'jurisdiction' => 'required|string|max:64', 'origin' => 'nullable|string', 'destination' => 'nullable|string', 'rate' => 'nullable|numeric|min:0|max:100', 'taxable_base' => 'nullable|numeric|min:0', 'liability' => 'nullable|numeric', 'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'metadata' => 'nullable|array']);

        return new SalesTaxResource($action->handle($data));
    }

    public function activate(SalesTaxRecord $record, ActivateSalesTaxRecord $action): SalesTaxResource
    {
        return new SalesTaxResource($action->handle($record));
    }

    public function close(SalesTaxRecord $record, CloseSalesTaxRecord $action): SalesTaxResource
    {
        return new SalesTaxResource($action->handle($record));
    }
}
