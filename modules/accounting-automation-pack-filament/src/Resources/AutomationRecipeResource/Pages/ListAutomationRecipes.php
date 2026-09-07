<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPackFilament\Resources\AutomationRecipeResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\AutomationPackFilament\Resources\AutomationRecipeResource;

final class ListAutomationRecipes extends ListRecords
{
    protected static string $resource = AutomationRecipeResource::class;
}
