<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionLivewire\Livewire;

use Liberu\Accounting\AnomalyDetection\Queries\AnomalyQuery;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class Anomalies extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function render(): mixed
    {
        return view('accounting-anomaly-detection::anomalies', ['anomalies' => app(AnomalyQuery::class)->paginate((int) (auth()->user()?->current_team_id ?? -1), $this->status ?: null)]);
    }
}
