<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class JobEstimateResource extends Resource
{
    protected static ?string $model = JobEstimate::class;

    protected static ?string $navigationLabel = 'Job estimates';

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->when($teamId !== null, fn (Builder $query): Builder => $query->where('team_id', $teamId));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('estimate_ref')->required(), TextInput::make('project_ref')->required(), TextInput::make('title')->required(), TextInput::make('currency')->required()->length(3), TextInput::make('total_cost')->numeric()->disabled(), TextInput::make('total_revenue')->numeric()->disabled()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('estimate_ref')->searchable(), TextColumn::make('project_ref')->searchable(), TextColumn::make('title'), TextColumn::make('currency'), TextColumn::make('total_cost'), TextColumn::make('total_revenue'), TextColumn::make('status')->badge()->sortable(), TextColumn::make('version_no')->sortable()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListJobEstimates::route('/')];
    }
}
