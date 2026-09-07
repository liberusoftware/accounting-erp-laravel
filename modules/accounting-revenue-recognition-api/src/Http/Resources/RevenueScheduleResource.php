<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;
use Liberu\Accounting\RevenueRecognition\Models\RevenueScheduleEntry;

/** @mixin RevenueSchedule */
final class RevenueScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-revenue-recognition', 'attributes' => ['obligation_id' => (string) $this->obligation_id, 'total_amount' => (float) $this->total_amount, 'start_date' => $this->start_date->toDateString(), 'periods' => $this->periods, 'deferred_account_ref' => $this->deferred_account_ref, 'revenue_account_ref' => $this->revenue_account_ref, 'status' => $this->status->value, 'funded' => $this->funded, 'entries' => $this->whenLoaded('entries', fn (): array => $this->entries->map(fn (RevenueScheduleEntry $entry): array => ['id' => (string) $entry->id, 'period_number' => $entry->period_number, 'recognition_date' => $entry->recognition_date->toDateString(), 'amount' => (float) $entry->amount, 'status' => $entry->status->value, 'ledger_reference' => $entry->ledger_reference])->all())]];
    }
}
