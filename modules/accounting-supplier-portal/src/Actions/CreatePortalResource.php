<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierPortal\Enums\PortalResourceType;
use Liberu\Accounting\SupplierPortal\Enums\PortalStatus;
use Liberu\Accounting\SupplierPortal\Events\PortalResourceCreated;
use Liberu\Accounting\SupplierPortal\Exceptions\InvalidPortalResource;
use Liberu\Accounting\SupplierPortal\Models\PortalResource;

final class CreatePortalResource
{
    public function handle(array $attributes): PortalResource
    {
        return DB::transaction(function () use ($attributes): PortalResource {
            $type = $attributes['type'] ?? null;
            if ($type instanceof PortalResourceType) {
                $type = $type->value;
            }if (! is_string($type) || ! in_array($type, array_column(PortalResourceType::cases(), 'value'), true)) {
                throw new InvalidPortalResource('A valid supplier portal resource type is required.');
            }foreach (['supplier_id', 'reference', 'currency'] as $key) {
                if (blank($attributes[$key] ?? null)) {
                    throw new InvalidPortalResource("Portal field [{$key}] is required.");
                }
            }if ((float) ($attributes['amount'] ?? 0) < 0) {
                throw new InvalidPortalResource('Portal amount must not be negative.');
            }if (PortalResource::query()->where(['supplier_id' => $attributes['supplier_id'], 'type' => $type, 'reference' => $attributes['reference']])->exists()) {
                throw new InvalidPortalResource('A resource with this supplier reference already exists.');
            }$resource = PortalResource::create(array_merge($attributes, ['type' => $type, 'status' => $attributes['status'] ?? PortalStatus::Draft]));
            DB::afterCommit(fn () => event(new PortalResourceCreated($resource)));

            return $resource;
        });
    }
}
