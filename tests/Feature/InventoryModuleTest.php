<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Module;
use Liberu\Accounting\Erp\Modules\Inventory\InventoryModule;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\InventoryItems\InventoryItemResource;
use Tests\TestCase;

class InventoryModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_by_default_when_unmanaged(): void
    {
        $this->assertTrue(InventoryModule::isActive());
        $this->assertTrue(InventoryItemResource::canAccess());
    }

    public function test_disabling_module_removes_resource_access(): void
    {
        Module::create(['name' => InventoryModule::KEY, 'enabled' => false]);

        $this->assertFalse(InventoryModule::isActive());
        $this->assertFalse(InventoryItemResource::canAccess());
        $this->assertFalse(InventoryItemResource::shouldRegisterNavigation());
    }

    public function test_enabling_module_restores_resource_access(): void
    {
        Module::create(['name' => InventoryModule::KEY, 'enabled' => true]);

        $this->assertTrue(InventoryModule::isActive());
        $this->assertTrue(InventoryItemResource::canAccess());
    }
}
