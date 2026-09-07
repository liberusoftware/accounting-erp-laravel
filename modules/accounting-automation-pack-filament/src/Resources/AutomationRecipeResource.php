<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPackFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\AutomationPack\Models\AutomationRecipe;
use Liberu\Accounting\AutomationPackFilament\Resources\AutomationRecipeResource\Pages\ListAutomationRecipes;

final class AutomationRecipeResource extends Resource
{
    protected static ?string $model = AutomationRecipe::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Automation';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('trigger')->required()->maxLength(120),
            TextInput::make('action')->required()->maxLength(120),
            TextInput::make('schedule')->maxLength(120),
            Select::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('trigger')->searchable(),
            TextColumn::make('action')->searchable(),
            TextColumn::make('status')->badge(),
        ])->defaultSort('name');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => ListAutomationRecipes::route('/')];
    }
}
