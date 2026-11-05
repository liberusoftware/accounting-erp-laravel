<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Erp\Models\AssetAcquisition;
use Liberu\Accounting\Erp\Modules\FixedAssets\FixedAssetsModule;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\Pages\CreateAssetAcquisition;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\Pages\EditAssetAcquisition;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\Pages\ListAssetAcquisitions;

class AssetAcquisitionResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Assets';

    #[\Override]
    protected static ?string $model = AssetAcquisition::class;

    // Gated by the FixedAssets module: disabling it removes this resource.
    #[\Override]
    public static function canAccess(): bool
    {
        return FixedAssetsModule::isActive();
    }

    #[\Override]
    public static function shouldRegisterNavigation(): bool
    {
        return FixedAssetsModule::isActive();
    }

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('acquisition_date')
                    ->numeric(),
                TextInput::make('acquisition_price')
                    ->numeric()
                    ->step('0.01')
                    ->minValue('0'),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('acquisition_date')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('acquisition_price')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.asset_name')
                    ->label('Asset Name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListAssetAcquisitions::route('/'),
            'create' => CreateAssetAcquisition::route('/create'),
            'edit' => EditAssetAcquisition::route('/{record}/edit'),
        ];
    }
}
