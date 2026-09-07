<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceiptsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\AddReceiptAttachment;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\AddReceiptLine;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\ConfirmService;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\CreateReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\PostAccrual;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\ReturnReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptStatus;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Queries\ReceiptQuery;
use Liberu\Accounting\GoodsAndServiceReceiptsApi\Http\Resources\ReceiptResource;

final class ReceiptsController extends Controller
{
    public function __construct(private readonly ReceiptQuery $query) {}

    public function index(Request $r): ReceiptResource
    {
        $s = $r->filled('status') ? ReceiptStatus::from($r->string('status')->toString()) : null;

        return new ReceiptResource($this->query->paginate($r->integer('team_id') ?: null, $s, $r->integer('per_page', 25)));
    }

    public function store(Request $r, CreateReceipt $a): ReceiptResource
    {
        return new ReceiptResource($a->handle($r->all()));
    }

    public function show(Receipt $receipt): ReceiptResource
    {
        return new ReceiptResource($receipt->load(['lines', 'confirmations', 'returns', 'attachments', 'accruals']));
    }

    public function line(Request $r, Receipt $receipt, AddReceiptLine $a): ReceiptResource
    {
        $d = $r->validate(['line_ref' => 'required|string|max:100', 'item_ref' => 'nullable|string|max:255', 'description' => 'required|string|max:1000', 'ordered_quantity' => 'nullable|numeric|min:0', 'received_quantity' => 'required|numeric|min:0.0001', 'unit_price' => 'required|numeric|min:0', 'inventory_ref' => 'nullable|string|max:255', 'project_ref' => 'nullable|string|max:255']);
        $a->handle($receipt, $d);

        return new ReceiptResource($receipt->refresh()->load('lines'));
    }

    public function confirmService(Request $r, Receipt $receipt, ConfirmService $a): ReceiptResource
    {
        $d = $r->validate(['confirmation_ref' => 'required|string|max:100', 'service_period' => 'required|string|max:100', 'quantity' => 'required|numeric|min:0.0001', 'value' => 'required|numeric|min:0', 'confirmed_by' => 'required|string|max:255', 'comment' => 'nullable|string|max:5000']);
        $a->handle($receipt, $d);

        return new ReceiptResource($receipt->refresh()->load('confirmations'));
    }

    public function return(Request $r, Receipt $receipt, ReturnReceipt $a): ReceiptResource
    {
        $d = $r->validate(['return_ref' => 'required|string|max:100', 'line_ref' => 'required|string|max:100', 'quantity' => 'required|numeric|min:0.0001', 'value' => 'required|numeric|min:0', 'reason' => 'required|string|max:1000', 'source_ref' => 'required|string|max:255']);
        $a->handle($receipt, $d);

        return new ReceiptResource($receipt->refresh()->load(['lines', 'returns']));
    }

    public function attachment(Request $r, Receipt $receipt, AddReceiptAttachment $a): ReceiptResource
    {
        $d = $r->validate(['attachment_ref' => 'required|string|max:100', 'kind' => 'required|string|max:100', 'file_ref' => 'required|string|max:500', 'description' => 'nullable|string|max:5000', 'checksum' => 'nullable|string|max:255', 'attached_by' => 'required|string|max:255', 'metadata' => 'nullable|array']);
        $a->handle($receipt, $d);

        return new ReceiptResource($receipt->load('attachments'));
    }

    public function accrual(Request $r, Receipt $receipt, PostAccrual $a): ReceiptResource
    {
        $d = $r->validate(['accrual_ref' => 'required|string|max:100', 'amount' => 'required|numeric|min:0.01', 'currency' => 'nullable|string|size:3', 'period_ref' => 'required|string|max:100', 'source_ref' => 'required|string|max:255', 'metadata' => 'nullable|array']);
        $a->handle($receipt, $d);

        return new ReceiptResource($receipt->load('accruals'));
    }

    public function variance(Receipt $receipt, ReceiptQuery $q): JsonResponse
    {
        return response()->json(['data' => $q->variance($receipt)]);
    }
}
