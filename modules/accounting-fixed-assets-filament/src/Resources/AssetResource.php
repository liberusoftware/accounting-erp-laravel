<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssetsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\FixedAssets\Models\Asset;

final class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationLabel = 'Fixed assets';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('asset_ref')->required(), TextInput::make('name')->required(), TextInput::make('category_id')->numeric()->required(), TextInput::make('acquired_on')->required(), TextInput::make('cost')->numeric()->required(), TextInput::make('salvage_value')->numeric(), TextInput::make('currency')->required()->length(3), TextInput::make('location_ref'), TextInput::make('custodian_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('asset_ref')->searchable(), TextColumn::make('name'), TextColumn::make('category.name'), TextColumn::make('cost'), TextColumn::make('net_book_value'), TextColumn::make('currency'), TextColumn::make('status')->badge(), TextColumn::make('custodian_ref')])->defaultSort('asset_ref');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAssets::route('/')];
    }
}
