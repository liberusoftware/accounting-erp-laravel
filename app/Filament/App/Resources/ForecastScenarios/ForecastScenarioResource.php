<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ForecastScenarios;

use App\Filament\App\Resources\ForecastScenarios\Pages\CreateForecastScenario;
use App\Filament\App\Resources\ForecastScenarios\Pages\EditForecastScenario;
use App\Filament\App\Resources\ForecastScenarios\Pages\ListForecastScenarios;
use App\Models\ForecastScenario;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

// Team scoping + team_id stamping are handled automatically by the App panel's
// Filament tenancy (->tenant(Team::class, ownershipRelationship: 'team')), the
// same as every other team-scoped resource here — this one uses the default.
class ForecastScenarioResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    #[\Override]
    protected static ?string $model = ForecastScenario::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Repeater::make('lines')
                    ->relationship()
                    ->schema([
                        Select::make('account_type')
                            ->options([
                                'revenue' => 'Revenue',
                                'cost_of_goods_sold' => 'Cost of Goods Sold',
                                'expense' => 'Expense',
                            ])
                            // One line per type — a duplicate type would be silently
                            // dropped by the pluck() keyed on account_type at apply time.
                            ->distinct()
                            ->required(),
                        TextInput::make('adjustment_pct')
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lines_count')
                    ->counts('lines')
                    ->label('Lines'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListForecastScenarios::route('/'),
            'create' => CreateForecastScenario::route('/create'),
            'edit' => EditForecastScenario::route('/{record}/edit'),
        ];
    }
}
