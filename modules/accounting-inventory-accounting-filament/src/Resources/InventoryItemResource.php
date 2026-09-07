<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccountingFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;

final class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static ?string $navigationLabel = 'Inventory accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('item_ref')->required(), TextInput::make('description')->required(), TextInput::make('warehouse_ref')->required(), TextInput::make('currency')->required()->length(3), TextInput::make('valuation_method')->required(), TextInput::make('quantity_on_hand')->disabled(), TextInput::make('inventory_value')->disabled()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('item_ref')->searchable(), TextColumn::make('description'), TextColumn::make('warehouse_ref')->searchable(), TextColumn::make('valuation_method')->badge(), TextColumn::make('quantity_on_hand'), TextColumn::make('inventory_value'), TextColumn::make('status')->badge()])->defaultSort('item_ref');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListInventoryItems::route('/')];
    }
}
