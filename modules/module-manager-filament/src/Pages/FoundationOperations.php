<?php

namespace Liberu\Foundation\ModuleManagerFilament\Pages;

use Filament\Pages\Page;
use Liberu\Foundation\ModuleManager\ModuleRegistry;
use Liberu\Foundation\Observability\Contracts\ObservabilityActor;
use Livewire\Attributes\Computed;

final class FoundationOperations extends Page
{
    protected string $view = 'module-manager-filament::pages.foundation-operations';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Foundation Operations';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    #[Computed]
    public function modules(): array
    {
        return array_map(
            fn ($manifest): array => $manifest->toArray(),
            app(ModuleRegistry::class)->all(),
        );
    }

    public static function canAccess(): bool
    {
        $actor = auth()->user();

        return $actor instanceof ObservabilityActor && $actor->isAdmin();
    }
}
