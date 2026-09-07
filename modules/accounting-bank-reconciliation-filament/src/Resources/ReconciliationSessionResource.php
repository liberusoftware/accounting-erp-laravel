<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliationFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;

final class ReconciliationSessionResource extends Resource
{
    protected static ?string $model = ReconciliationSession::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Bank Reconciliation';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('bank_account_id')->numeric()->required(), TextInput::make('period_start')->required(), TextInput::make('period_end')->required(), TextInput::make('opening_balance')->numeric()->required(), TextInput::make('statement_balance')->numeric()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('bank_account_id')->sortable(), TextColumn::make('period_start')->date(), TextColumn::make('period_end')->date(), TextColumn::make('statement_balance')->numeric(), TextColumn::make('status')->badge()])->defaultSort('period_end', 'desc');
    }
}
