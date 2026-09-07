<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRulesLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\BankRules\Actions\TestBankRule;
use Liberu\Accounting\BankRules\Models\BankRule;
use Livewire\Component;

final class Rules extends Component
{
    public string $transactionText = '';

    public string $testResult = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view bank rules.');
        }
    }

    public function testRule(int $ruleId): void
    {
        $rule = $this->scoped()->findOrFail($ruleId);
        $result = app(TestBankRule::class)->handle($rule, ['description' => $this->transactionText]);
        $this->testResult = $result['matched'] ? 'Rule matched.' : 'Rule did not match.';
    }

    public function render(): mixed
    {
        return view('accounting-bank-rules::rules', ['rules' => $this->scoped()->get()]);
    }

    private function scoped(): mixed
    {
        return BankRule::query()->where('team_id', auth()->user()->current_team_id)->orderByDesc('priority')->orderBy('name');
    }
}
