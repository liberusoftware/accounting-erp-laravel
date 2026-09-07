<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\ForecastScenario;
use App\Services\ForecastService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

/**
 * Pick a forecast scenario + period count and compare the rolling baseline
 * against the scenario-adjusted projection (net income per period).
 */
/**
 * @property-read Schema $form
 */
class ForecastComparison extends Page
{
    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    #[\Override]
    protected static ?string $title = 'Forecast Comparison';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    #[\Override]
    protected string $view = 'filament.app.pages.forecast-comparison';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var array<string, mixed>|null */
    public ?array $result = null;

    public function mount(): void
    {
        $this->form->fill(['periods' => 3]);
    }

    /**
     * Scenarios belonging to the current tenant team only.
     *
     * @return array<int, string>
     */
    public function visibleScenarios(): array
    {
        // -1 sentinel (not null) so a tenantless caller gets an empty set rather
        // than leaking team_id IS NULL rows — the repo-wide convention.
        return ForecastScenario::where('team_id', Filament::getTenant()?->getKey() ?? -1)->pluck('name', 'id')->all();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('forecast_scenario_id')
                    ->label('Scenario')
                    ->options(fn (): array => $this->visibleScenarios())
                    ->required(),
                TextInput::make('periods')
                    ->numeric()
                    ->minValue(1)
                    ->default(3)
                    ->required(),
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
        $scenarioId = (int) ($data['forecast_scenario_id'] ?? 0);

        // Only compare against a scenario the current team actually owns.
        if (! array_key_exists($scenarioId, $this->visibleScenarios())) {
            return;
        }

        $scenario = ForecastScenario::findOrFail($scenarioId);
        $periods = (int) ($data['periods'] ?? 3);

        // Baseline MUST use the same team as the scenario dropdown (the active
        // tenant) — not auth()->current_team_id, which can drift out of sync and
        // would render one team's actuals under another team's label.
        $this->result = app(ForecastService::class)->compare(
            $periods,
            $scenario,
            (int) (Filament::getTenant()?->getKey() ?? -1),
        );
    }
}
