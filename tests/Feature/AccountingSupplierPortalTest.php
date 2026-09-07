<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\SupplierPortal\Actions\AttachPortalDocument;
use Liberu\Accounting\SupplierPortal\Actions\CreatePortalResource;
use Liberu\Accounting\SupplierPortal\Actions\TransitionPortalResource;
use Liberu\Accounting\SupplierPortal\Enums\PortalResourceType;
use Liberu\Accounting\SupplierPortal\Enums\PortalStatus;
use Liberu\Accounting\SupplierPortal\Exceptions\InvalidPortalResource;
use Liberu\Accounting\SupplierPortal\Queries\PortalResourceQuery;

uses(RefreshDatabase::class);
it('runs a supplier invoice through review, approval, and payment', function (): void {
    $resource = app(CreatePortalResource::class)->handle(['supplier_id' => 'supplier-1', 'type' => PortalResourceType::Invoice, 'reference' => 'INV-1', 'currency' => 'USD', 'amount' => 125]);
    app(TransitionPortalResource::class)->handle($resource, PortalStatus::Submitted);
    app(TransitionPortalResource::class)->handle($resource->refresh(), PortalStatus::UnderReview);
    app(TransitionPortalResource::class)->handle($resource->refresh(), PortalStatus::Approved);
    $paid = app(TransitionPortalResource::class)->handle($resource->refresh(), PortalStatus::Paid);
    expect($paid->status)->toBe(PortalStatus::Paid);
});
it('requires rejection reasons and deduplicates secure documents', function (): void {
    $resource = app(CreatePortalResource::class)->handle(['supplier_id' => 'supplier-2', 'type' => 'profile_change', 'reference' => 'BANK-1', 'currency' => 'USD']);
    app(TransitionPortalResource::class)->handle($resource, PortalStatus::Submitted);
    expect(fn () => app(TransitionPortalResource::class)->handle($resource->refresh(), PortalStatus::Rejected))->toThrow(InvalidPortalResource::class);
    $document = ['path' => 'private/bank.pdf', 'original_name' => 'bank.pdf', 'mime_type' => 'application/pdf', 'sha256' => hash('sha256', 'bank')];
    app(AttachPortalDocument::class)->handle($resource, $document);
    app(AttachPortalDocument::class)->handle($resource->refresh(), $document);
    expect($resource->refresh()->documents)->toHaveCount(1);
});
it('rejects duplicate references and queries by supplier and status', function (): void {
    $data = ['supplier_id' => 'supplier-3', 'type' => 'purchase_order', 'reference' => 'PO-1', 'currency' => 'USD'];
    app(CreatePortalResource::class)->handle($data);
    expect(fn () => app(CreatePortalResource::class)->handle($data))->toThrow(InvalidPortalResource::class);
    expect(app(PortalResourceQuery::class)->paginate('supplier-3', 'purchase_order', 'draft')->total())->toBe(1);
});
