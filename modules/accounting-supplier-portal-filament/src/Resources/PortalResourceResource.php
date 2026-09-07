<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortalFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\SupplierPortal\Models\PortalResource;
use Liberu\Accounting\SupplierPortalFilament\Resources\PortalResourceResource\Pages\ListPortalResources;

final class PortalResourceResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $model = PortalResource::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('supplier_id')->required(), TextInput::make('type')->required(), TextInput::make('reference')->required(), TextInput::make('currency')->required(), TextInput::make('amount')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('supplier_id')->searchable(), TextColumn::make('type')->badge(), TextColumn::make('reference')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('amount'), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPortalResources::route('/')];
    }
}
