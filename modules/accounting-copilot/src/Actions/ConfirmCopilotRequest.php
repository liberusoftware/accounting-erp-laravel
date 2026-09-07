<?php

declare(strict_types=1);

namespace Liberu\Accounting\Copilot\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Copilot\Models\CopilotRequest;

final class ConfirmCopilotRequest
{
    public function handle(CopilotRequest $request, string $confirmationKey): CopilotRequest
    {
        abort_if($request->confirmation_key !== $confirmationKey, 422, 'Confirmation key is invalid.');
        abort_if($request->status !== 'awaiting_confirmation', 409, 'This request is no longer awaiting confirmation.');

        return DB::transaction(function () use ($request): CopilotRequest {
            $request->update(['status' => 'confirmed']);

            return $request->refresh();
        });
    }
}
