<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\AddReceiptAttachment;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\AddReceiptLine;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\CreateReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\PostAccrual;
use Liberu\Accounting\GoodsAndServiceReceipts\Actions\ReturnReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Exceptions\InvalidReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Queries\ReceiptQuery;
use Tests\TestCase;

final class AccountingGoodsAndServiceReceiptsTest extends TestCase
{
    use RefreshDatabase;

    public function test_goods_receipt_tracks_quantities_variance_returns_attachments_and_accruals(): void
    {
        $receipt = app(CreateReceipt::class)->handle(['receipt_ref' => 'GRN-001', 'receipt_type' => 'goods', 'supplier_ref' => 'SUP-1', 'currency' => 'gbp', 'inventory_ref' => 'WH-1', 'project_ref' => 'JOB-1']);
        app(AddReceiptLine::class)->handle($receipt, ['line_ref' => 'L-1', 'item_ref' => 'SKU-1', 'description' => 'Widget', 'ordered_quantity' => 10, 'received_quantity' => 8, 'unit_price' => 12]);
        app(AddReceiptAttachment::class)->handle($receipt, ['attachment_ref' => 'ATT-1', 'kind' => 'delivery-note', 'file_ref' => 'file-1', 'attached_by' => 'user-1']);
        app(ReturnReceipt::class)->handle($receipt, ['return_ref' => 'RET-1', 'line_ref' => 'L-1', 'quantity' => 2, 'value' => 24, 'reason' => 'Damaged', 'source_ref' => 'RMA-1']);
        app(PostAccrual::class)->handle($receipt, ['accrual_ref' => 'ACC-1', 'amount' => 96, 'period_ref' => '2026-08', 'source_ref' => 'GRN-001']);

        $variance = app(ReceiptQuery::class)->variance($receipt);
        $this->assertSame(-2.0, $variance['quantity_variance']);
        $this->assertSame(-24.0, $variance['value_variance']);
        $this->assertCount(1, $receipt->refresh()->attachments);
        $this->assertCount(1, $receipt->refresh()->accruals);
    }

    public function test_return_cannot_exceed_received_quantity(): void
    {
        $receipt = app(CreateReceipt::class)->handle(['receipt_ref' => 'GRN-002', 'receipt_type' => 'goods', 'supplier_ref' => 'SUP-2', 'currency' => 'USD']);
        app(AddReceiptLine::class)->handle($receipt, ['line_ref' => 'L-1', 'description' => 'Widget', 'ordered_quantity' => 1, 'received_quantity' => 1, 'unit_price' => 10]);
        $this->expectException(InvalidReceipt::class);
        app(ReturnReceipt::class)->handle($receipt, ['return_ref' => 'RET-2', 'line_ref' => 'L-1', 'quantity' => 2, 'value' => 20, 'reason' => 'Excess', 'source_ref' => 'RMA-2']);
    }
}
