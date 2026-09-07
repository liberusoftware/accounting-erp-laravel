<?php

declare(strict_types=1);

namespace Liberu\Accounting\IntercompanyApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction; /** @mixin IntercompanyTransaction */
final class IntercompanyTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $t = $this->resource;

        return ['id' => $t->getKey(), 'transaction_ref' => $t->transaction_ref, 'source_entity_ref' => $t->source_entity_ref, 'target_entity_ref' => $t->target_entity_ref, 'transaction_type' => $t->transaction_type, 'description' => $t->description, 'amount' => $t->amount, 'currency' => $t->currency, 'status' => $t->status->value, 'transaction_date' => $t->transaction_date, 'counterparty' => $this->whenLoaded('counterparty'), 'confirmations' => $this->whenLoaded('confirmations'), 'settlements' => $this->whenLoaded('settlements'), 'differences' => $this->whenLoaded('differences'), 'evidence' => $this->whenLoaded('evidence')];
    }
}
