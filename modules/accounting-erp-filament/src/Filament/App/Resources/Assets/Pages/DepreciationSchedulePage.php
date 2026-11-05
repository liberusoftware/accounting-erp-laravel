<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Assets\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Liberu\Accounting\Erp\Models\Asset;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Assets\AssetResource;

class DepreciationSchedulePage extends Page
{
    #[\Override]
    protected static string $resource = AssetResource::class;

    #[\Override]
    protected string $view = 'accounting-erp-filament::filament.app.resources.asset-resource.pages.depreciation-schedule-page';

    public Asset $record;

    public function mount(Asset $record): void
    {
        $this->record = $record;
    }

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->url(fn (): string => AssetResource::getUrl())
                ->label('Back to Assets')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
