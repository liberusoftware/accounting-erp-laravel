<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\ConsolidationGroup;
use App\Services\ConsolidationService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * Group-level consolidated financial statements. Pick a consolidation group +
 * period and see combined / eliminations / consolidated P&L, balance sheet, and
 * cash flow. A user only sees groups that include a team they belong to.
 */
/**
 * @property-read Schema $form
 */
class ConsolidatedReports extends Page
{
    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    #[\Override]
    protected static ?string $title = 'Consolidated Reports';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    #[\Override]
    protected string $view = 'filament.app.pages.consolidated-reports';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var array<string, mixed>|null */
    public ?array $profitAndLoss = null;

    /** @var array<string, mixed>|null */
    public ?array $balanceSheet = null;

    /** @var array<string, mixed>|null */
    public ?array $cashFlow = null;

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfYear()->toDateString(),
            'end_date' => now()->endOfYear()->toDateString(),
        ]);
    }

    /**
     * Groups the current user may view: those including a team the user belongs to.
     *
     * @return array<int, string>
     */
    public function visibleGroups(): array
    {
        $teamIds = Auth::user()?->allTeams()->pluck('id')->all() ?? [];

        return ConsolidationGroup::whereHas('members', fn ($q) => $q->whereIn('teams.id', $teamIds))
            ->pluck('name', 'id')
            ->all();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('consolidation_group_id')
                    ->label('Consolidation group')
                    ->options(fn (): array => $this->visibleGroups())
                    ->required(),
                DatePicker::make('start_date')->required(),
                DatePicker::make('end_date')->required(),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Group::make([
                EmbeddedSchema::make('form'),
                Actions::make([
                    Action::make('generate')
                        ->label('Generate')
                        ->action(fn () => $this->generate()),
                ]),
            ]),
        ]);
    }

    public function generate(): void
    {
        $data = $this->data ?? [];
        $groupId = (int) ($data['consolidation_group_id'] ?? 0);

        // Only produce a report for a group the user may actually see.
        if (! array_key_exists($groupId, $this->visibleGroups())) {
            return;
        }

        $group = ConsolidationGroup::findOrFail($groupId);
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        $service = app(ConsolidationService::class);
        $this->profitAndLoss = $service->consolidatedProfitAndLoss($group, $start, $end);
        $this->balanceSheet = $service->consolidatedBalanceSheet($group, $end);
        $this->cashFlow = $service->consolidatedCashFlow($group, $start, $end);
    }
}
