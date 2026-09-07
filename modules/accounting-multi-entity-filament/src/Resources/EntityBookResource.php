<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntityFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\MultiEntity\Models\EntityBook;

final class EntityBookResource extends Resource
{
    protected static ?string $model = EntityBook::class;

    protected static ?string $navigationLabel = 'Entity Books';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('entity_ref')->required(), TextInput::make('code')->required(), TextInput::make('name')->required(), TextInput::make('base_currency')->required()->length(3), TextInput::make('timezone')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('code')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('base_currency'), TextColumn::make('status')->badge(), TextColumn::make('access_count')->counts('access'), TextColumn::make('period_count')->counts('periods')])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'suspended' => 'Suspended', 'archived' => 'Archived'])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListEntityBooks::route('/')];
    }
}
