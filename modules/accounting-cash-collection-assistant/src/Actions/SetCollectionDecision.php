<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistant\Actions;

use Liberu\Accounting\CashCollectionAssistant\Models\CashCollectionAssistant;

final class SetCollectionDecision
{
    public function handle(CashCollectionAssistant $assistant, string $field, string $value): CashCollectionAssistant
    {
        $allowed = ['approval_status' => ['pending', 'approved', 'rejected', 'not_required'], 'promise_status' => ['open', 'kept', 'broken', 'none'], 'outcome' => ['paid', 'partial', 'disputed', 'no_response', 'written_off']];
        if (isset($allowed[$field]) && in_array($value, $allowed[$field], true)) {
            $assistant->forceFill([$field => $value])->save();
        }

        return $assistant->refresh();
    }
}
