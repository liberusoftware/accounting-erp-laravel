<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitabilityFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\ProjectProfitability\Models\ProjectProfitability;

final class ProjectProfitabilityResource extends Resource
{
    protected static ?string $model = ProjectProfitability::class;

    protected static ?string $navigationLabel = 'Project profitability';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('project_job_id')->numeric()->required(), TextInput::make('period_start')->required(), TextInput::make('period_end')->required(), TextInput::make('currency')->required()->length(3), TextInput::make('revenue_amount')->numeric(), TextInput::make('cost_amount')->numeric(), TextInput::make('estimate_amount')->numeric(), TextInput::make('billed_amount')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('project_job_id')->sortable(), TextColumn::make('period_start')->date()->sortable(), TextColumn::make('period_end')->date()->sortable(), TextColumn::make('currency'), TextColumn::make('revenue_amount'), TextColumn::make('cost_amount'), TextColumn::make('status')->badge()])->defaultSort('period_start', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListProjectProfitability::route('/')];
    }
}
