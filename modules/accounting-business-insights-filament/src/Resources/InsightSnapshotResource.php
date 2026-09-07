<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\BusinessInsights\Models\InsightSnapshot;

final class InsightSnapshotResource extends Resource
{
    protected static ?string $model = InsightSnapshot::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Business Insights';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('metric')->required(), TextInput::make('value')->numeric()->required(), TextInput::make('unit')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('metric')->searchable(), TextColumn::make('period_start')->date(), TextColumn::make('period_end')->date(), TextColumn::make('value'), TextColumn::make('unit')]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListInsightSnapshots::route('/'), 'create' => Pages\CreateInsightSnapshot::route('/create')];
    }
}
