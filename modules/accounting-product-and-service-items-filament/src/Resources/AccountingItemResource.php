<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsFilament\Resources;

use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\ProductAndServiceItems\Models\AccountingItem;
use Liberu\Accounting\ProductAndServiceItemsFilament\Resources\AccountingItemResource\Pages\ListAccountingItems;

final class AccountingItemResource extends Resource
{
    protected static ?string $model = AccountingItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Items and services';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('kind')->badge(),
            TextColumn::make('unit')->placeholder('—'),
            TextColumn::make('sales_price')->money(fn (AccountingItem $record): string => $record->currency),
            TextColumn::make('purchase_price')->money(fn (AccountingItem $record): string => $record->currency),
            TextColumn::make('status')->badge(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListAccountingItems::route('/')];
    }
}
