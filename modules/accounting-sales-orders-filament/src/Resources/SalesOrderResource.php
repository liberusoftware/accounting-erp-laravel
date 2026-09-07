<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrdersFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;
use Liberu\Accounting\SalesOrdersFilament\Resources\SalesOrderResource\Pages\ListSalesOrders;

final class SalesOrderResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $model = SalesOrder::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('customer_id')->required(), TextInput::make('order_number')->required(), TextInput::make('currency')->required(), TextInput::make('order_date')->type('date')->required(), TextInput::make('total')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('order_number')->searchable(), TextColumn::make('customer_id')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('total'), TextColumn::make('invoiced_total'), TextColumn::make('order_date')->date()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => ListSalesOrders::route('/')];
    }
}
