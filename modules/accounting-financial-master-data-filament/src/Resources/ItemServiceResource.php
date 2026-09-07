<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\FinancialMasterData\Models\ItemService;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource\Pages\CreateItemService;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource\Pages\EditItemService;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource\Pages\ListItemServices;

final class ItemServiceResource extends Resource
{
    protected static ?string $model = ItemService::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('legal_entity_id')->required()->numeric(), TextInput::make('sku')->required()->maxLength(64), TextInput::make('name')->required()->maxLength(255), Select::make('kind')->required()->options(['item' => 'Item', 'service' => 'Service']), TextInput::make('unit')->maxLength(32), TextInput::make('sales_price')->numeric()->minValue(0), TextInput::make('purchase_price')->numeric()->minValue(0), TextInput::make('tax_profile_id')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('sku')->searchable()->sortable(), TextColumn::make('name')->searchable(), TextColumn::make('kind')->badge(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListItemServices::route('/'), 'create' => CreateItemService::route('/create'), 'edit' => EditItemService::route('/{record}/edit')];
    }
}
