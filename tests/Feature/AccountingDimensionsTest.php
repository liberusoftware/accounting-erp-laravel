<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Dimensions\Actions\AllocateDimensions;
use Liberu\Accounting\Dimensions\Actions\SaveDimension;
use Liberu\Accounting\Dimensions\Actions\SaveDimensionValue;
use Liberu\Accounting\Dimensions\Actions\ValidateDimensions;
use Liberu\Accounting\Dimensions\Exceptions\InvalidDimension;
use Tests\TestCase;

class AccountingDimensionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_allocations_must_total_one_hundred_percent_and_are_idempotent(): void
    {
        $allocations = app(AllocateDimensions::class)->handle('expense-001', 1000, [
            ['percentage' => 60, 'dimensions' => ['department' => ['sales']]],
            ['percentage' => 40, 'dimensions' => ['department' => ['support']]],
        ]);

        $this->assertCount(2, $allocations);
        $this->assertSame('600.00', (string) $allocations[0]->amount);

        $this->expectException(InvalidDimension::class);
        app(AllocateDimensions::class)->handle('expense-001', 1000, [
            ['percentage' => 100, 'dimensions' => ['department' => ['sales']]],
        ]);
    }

    public function test_required_dimensions_reject_missing_or_inactive_values(): void
    {
        $dimension = app(SaveDimension::class)->handle([
            'code' => 'department', 'name' => 'Department', 'kind' => 'department', 'is_required' => true,
        ]);
        app(SaveDimensionValue::class)->handle($dimension, [
            'code' => 'sales', 'name' => 'Sales', 'is_active' => true,
        ]);

        $this->expectException(InvalidDimension::class);
        app(ValidateDimensions::class)->handle([]);
    }
}
