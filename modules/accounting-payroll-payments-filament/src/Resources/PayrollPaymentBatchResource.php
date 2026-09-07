<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPaymentsFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\PayrollPayments\Actions\TransitionPayrollPayment;
use Liberu\Accounting\PayrollPayments\Enums\PaymentStatus;
use Liberu\Accounting\PayrollPayments\Models\PayrollPaymentBatch;
use Liberu\Accounting\PayrollPaymentsFilament\Resources\PayrollPaymentBatchResource\Pages\ListPayrollPaymentBatches;

final class PayrollPaymentBatchResource extends Resource
{
    protected static ?string $model = PayrollPaymentBatch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $navigationLabel = 'Payroll payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_ref')->label('Batch')->searchable()->sortable(),
                TextColumn::make('currency')->badge(),
                TextColumn::make('net_pay_amount')->money(fn (PayrollPaymentBatch $record): string => $record->currency),
                TextColumn::make('liability_amount')->money(fn (PayrollPaymentBatch $record): string => $record->currency),
                TextColumn::make('status')->badge(),
                TextColumn::make('provider')->placeholder('—'),
            ])
            ->recordActions([
                Action::make('transition')
                    ->label('Change status')
                    ->icon('heroicon-o-arrow-path')
                    ->schema([
                        Select::make('status')
                            ->options(collect(PaymentStatus::cases())->mapWithKeys(fn (PaymentStatus $status): array => [$status->value => ucfirst($status->value)])->all())
                            ->required(),
                    ])
                    ->action(function (PayrollPaymentBatch $record, array $data, TransitionPayrollPayment $transition): void {
                        $transition->handle($record, PaymentStatus::from((string) $data['status']));
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListPayrollPaymentBatches::route('/')];
    }
}
