<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReportingFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;

final class ReportPackResource extends Resource
{
    protected static ?string $model = ReportPack::class;

    protected static ?string $navigationLabel = 'Management Reports';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('report_ref')->required(), TextInput::make('name')->required(), DatePicker::make('period_start')->required(), DatePicker::make('period_end')->required(), TextInput::make('currency')->required()->length(3)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('report_ref')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('period_end')->date()->sortable(), TextColumn::make('currency'), TextColumn::make('status')->badge(), TextColumn::make('narratives_count')->counts('narratives'), TextColumn::make('charts_count')->counts('charts')])->defaultSort('period_end', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListReportPacks::route('/')];
    }
}
