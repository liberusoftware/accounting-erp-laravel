<?php

declare(strict_types=1);

namespace App\Services\Concerns;

use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Support\Str;

/**
 * Shared by the accounting-provider sync services (QBO, Xero, Sage): map an
 * external contact name onto a local Customer/Vendor, creating one if unseen.
 * Each provider passes its own tag (qbo/xero/sage) so imported placeholder
 * emails/phones stay attributable.
 */
trait ResolvesSyncedContacts
{
    protected function syncedCustomerId(string $name, string $tag, ?int $teamId = null): int
    {
        $ref = Str::random(8);

        $attributes = ['customer_name' => $name, 'team_id' => $teamId];

        $values = [
            'customer_last_name' => '',
            'customer_address' => 'Imported from '.$this->providerLabel($tag),
            'customer_email' => Str::slug($name).'.'.$ref.'@'.$tag.'.imported',
            'customer_phone' => $tag.'-'.$ref,
            'customer_city' => 'Unknown',
        ];
        if ($teamId !== null) {
            $values['team_id'] = $teamId;
        }

        $customer = Customer::firstOrCreate(
            $attributes,
            $values,
        );

        return (int) $customer->getKey();
    }

    protected function syncedVendorId(string $name, string $tag, ?int $teamId = null): int
    {
        $ref = Str::random(8);

        $attributes = ['name' => $name, 'team_id' => $teamId];

        $values = ['email' => Str::slug($name).'.'.$ref.'@'.$tag.'.imported'];
        if ($teamId !== null) {
            $values['team_id'] = $teamId;
        }

        $vendor = Vendor::firstOrCreate(
            $attributes,
            $values,
        );

        return (int) $vendor->getKey();
    }

    private function providerLabel(string $tag): string
    {
        return [
            'qbo' => 'QuickBooks Online',
            'xero' => 'Xero',
            'sage' => 'Sage',
        ][$tag] ?? ucfirst($tag);
    }
}
