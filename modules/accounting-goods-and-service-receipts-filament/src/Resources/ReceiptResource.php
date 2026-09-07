<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceiptsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;

final class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static ?string $navigationLabel = 'Goods and service receipts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('receipt_ref')->required(), TextInput::make('receipt_type')->required(), TextInput::make('supplier_ref')->required(), TextInput::make('purchase_order_ref'), TextInput::make('currency')->required()->length(3), TextInput::make('inventory_ref'), TextInput::make('project_ref'), TextInput::make('total_value')->disabled()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('receipt_ref')->searchable(), TextColumn::make('receipt_type')->badge(), TextColumn::make('supplier_ref')->searchable(), TextColumn::make('purchase_order_ref'), TextColumn::make('total_value'), TextColumn::make('currency'), TextColumn::make('status')->badge()->sortable(), TextColumn::make('received_at')->dateTime()->sortable()])->defaultSort('received_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListReceipts::route('/')];
    }
}
