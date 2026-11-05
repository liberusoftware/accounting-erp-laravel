<x-filament-panels::page>
    {{ $this->content }}

    @if ($result)
        <x-filament::section heading="Net income: baseline vs scenario">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="pr-4">Period</th>
                        <th class="pr-4">Baseline</th>
                        <th>Scenario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result['baseline_net_income'] as $period => $baseline)
                        <tr>
                            <td class="pr-4">{{ $period }}</td>
                            <td class="pr-4">{{ number_format($baseline, 2) }}</td>
                            <td>{{ number_format($result['scenario_net_income'][$period] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>
    @endif
</x-filament-panels::page>
