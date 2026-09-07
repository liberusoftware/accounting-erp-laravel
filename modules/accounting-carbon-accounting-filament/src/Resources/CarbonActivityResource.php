<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\CarbonAccounting\Models\CarbonActivity;

final class CarbonActivityResource extends Resource
{
    protected static ?string $model = CarbonActivity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Carbon Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([DatePicker::make('activity_date')->required(), TextInput::make('scope')->required(), TextInput::make('category')->required(), TextInput::make('description')->required(), TextInput::make('quantity')->numeric()->required(), TextInput::make('unit')->required(), TextInput::make('emission_factor')->numeric()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('activity_date')->date(), TextColumn::make('scope')->badge(), TextColumn::make('category')->searchable(), TextColumn::make('quantity'), TextColumn::make('unit'), TextColumn::make('co2e')]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListCarbonActivities::route('/'), 'create' => Pages\CreateCarbonActivity::route('/create')];
    }
}
