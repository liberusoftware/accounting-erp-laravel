<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigrationFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\SpreadsheetMigration\Models\MigrationTemplate;
use Liberu\Accounting\SpreadsheetMigrationFilament\Resources\MigrationTemplateResource\Pages\ListMigrationTemplates;

final class MigrationTemplateResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $model = MigrationTemplate::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('entity')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('entity')->badge(), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListMigrationTemplates::route('/')];
    }
}
