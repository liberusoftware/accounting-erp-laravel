<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpensesFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

final class ExpenseClaimResource extends Resource
{
    protected static ?string $model = ExpenseClaim::class;

    protected static ?string $navigationLabel = 'Employee expenses';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('employee_ref')->required(), TextInput::make('claim_ref')->required(), TextInput::make('team_id')->numeric(), Select::make('currency')->options(['GBP' => 'GBP', 'USD' => 'USD', 'EUR' => 'EUR'])->required(), TextInput::make('project_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('claim_ref')->searchable(), TextColumn::make('employee_ref'), TextColumn::make('status')->badge(), TextColumn::make('currency'), TextColumn::make('items_sum_amount')->label('Total')])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListExpenseClaims::route('/')];
    }
}
