<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBillingFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\ProjectBilling\Models\ProjectBilling;

final class ProjectBillingResource extends Resource
{
    protected static ?string $model = ProjectBilling::class;

    protected static ?string $navigationLabel = 'Project billing';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('project_job_id')->numeric()->required(), TextInput::make('method')->required(), TextInput::make('period_start')->required(), TextInput::make('period_end')->required(), TextInput::make('currency')->required()->length(3), TextInput::make('amount')->numeric(), TextInput::make('invoice_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('project_job_id')->sortable(), TextColumn::make('method')->badge(), TextColumn::make('period_start')->date()->sortable(), TextColumn::make('period_end')->date()->sortable(), TextColumn::make('currency'), TextColumn::make('amount'), TextColumn::make('status')->badge()])->defaultSort('period_start', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListProjectBilling::route('/')];
    }
}
