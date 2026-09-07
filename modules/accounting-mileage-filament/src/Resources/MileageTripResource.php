<?php

declare(strict_types=1);

namespace Liberu\Accounting\MileageFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Mileage\Models\MileageTrip;

final class MileageTripResource extends Resource
{
    protected static ?string $model = MileageTrip::class;

    protected static ?string $navigationLabel = 'Mileage Trips';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('trip_ref')->required(), TextInput::make('employee_ref')->required(), DatePicker::make('trip_date')->required(), TextInput::make('distance')->numeric()->required()->rule('gt:0'), TextInput::make('region')->required(), TextInput::make('currency')->required()->length(3), Textarea::make('business_purpose')->required(), TextInput::make('project_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('trip_ref')->searchable(), TextColumn::make('employee_ref')->searchable(), TextColumn::make('trip_date')->date()->sortable(), TextColumn::make('distance'), TextColumn::make('region'), TextColumn::make('reimbursement_amount'), TextColumn::make('status')->badge()])->defaultSort('trip_date', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListMileageTrips::route('/')];
    }
}
