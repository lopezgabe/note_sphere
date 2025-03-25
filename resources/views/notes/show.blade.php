<x-app-layout>
    <section>
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-4">Note</h1>

            <ul>
                @foreach(json_decode($note) as $key => $item)
                    <li><strong>{{ strtoupper(str_replace('_',' ',$key)) }}:</strong> {{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>
{{--    <section>--}}

{{--        @if ($note->simulationResults->isNotEmpty())--}}
{{--            <h3 class="text-2xl font-bold mb-4">Simulation Result</h3>--}}
{{--            <div class="container mx-auto p-6">--}}
{{--                <ul>--}}
{{--                    @foreach(($note->simulationResults[0]->result) as $key => $simulation)--}}
{{--                        <li><strong>{{ strtoupper(str_replace('_',' ',$key)) }}:</strong> {{ $simulation }}</li>--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        @endif--}}
{{--    </section>--}}
    <section>
        <div class="container mx-auto p-6">
            <h3 class="text-2xl font-bold mb-4">Simulation Result Analysis</h3>
            <h4 class="text-2xl font-bold mb-4">{{ $note->note_id }}</h4>
        @if (!empty($note->simulationResults[0]))
            <ul>
                <li>
                    <strong>Simulations:</strong> {{ $note->getNumber($note->simulationResults[0]->result['simulations']) }}
                </li>
                <li>
                    <strong>Purchase Price:</strong> {{ $note->getNumberCurrency($note->simulationResults[0]->result['purchase_price']) }}
                </li>
                <li>
                    <strong>Monthly Payment:</strong> {{ $note->getNumberCurrency($note->simulationResults[0]->result['monthly_pi']) }}
                </li>
                <li>
                    <strong>Baseline IRR:</strong> {{ $note->getNumberPercent($note->simulationResults[0]->result['baseline_irr']) }}
                </li>
                <li>
                    <strong>ITV:</strong> {{ $note->getNumberPercent($note->simulationResults[0]->result['itv']) }}
                    How much you are paying for the note based on the value. The lower the ITV the lower the risk.
                </li>

                <li>
                    <strong>Average IRR:</strong> {{ $note->simulationResults[0]->result['mean_irr'] !== NULL ? number_format($note->simulationResults[0]->result['mean_irr'] * 100, 2, '.', '') . '%' : 'N/A' }}
                    The Average Internal Rate of Return across all the trials. It's the expected annualized return, factoring in variability like defaults, prepayments, or recoveries.
                    A high Average IRR signals strong profit potential. <strong>Higher is better.</strong>
                </li>
                <li>
                    <strong>Median IRR:</strong> {{ $note->simulationResults[0]->result['median_irr'] !== NULL ? number_format($note->simulationResults[0]->result['median_irr'] * 100, 2, '.', '') . '%' : 'N/A' }}
                    The Median Internal Rate of Return: the middle IRR value (50th percentile) half the trials yield more, half yield less. Removes outliers.
                    Favor notes where the Median IRR is well above you minimum acceptable return (8-10%). <strong>Higher is better.</strong>
                </li>
                <li>
                    <strong>Standard Deviation:</strong> {{ $note->simulationResults[0]->result['std_irr'] !== NULL ? number_format($note->simulationResults[0]->result['std_irr'] * 100, 2, '.', '') . '%' : 'N/A' }}
                    Measures the variability of IRR outcomes. A higher Standard Deviation means returns fluctuate widely indicating higher uncertainty. <strong>Lower is better.</strong>
                </li>
                <li>
                    <strong>5th Percentile IRR:</strong> {{ $note->simulationResults[0]->result['percentile_5'] !== NULL ? number_format($note->simulationResults[0]->result['percentile_5'] * 100, 2, '.', '') . '%' : 'N/A' }}
                    The IRR in the worst 5% of scenarios. If this is positive then it's a green light. <strong>Higher is better.</strong>
                </li>
                <li>
                    <strong>95th Percentile IRR:</strong> {{ $note->simulationResults[0]->result['percentile_95'] !== NULL ? number_format($note->simulationResults[0]->result['percentile_95'] * 100, 2, '.', '') . '%' : 'N/A' }}
                    The IRR in the best 5% of scenarios. <strong>Higher is better.</strong>
                </li>
                <li>
                    <strong>Probability of Loss:</strong> {{ $note->simulationResults[0]->result['prob_loss'] !== NULL ? number_format($note->simulationResults[0]->result['prob_loss'] * 100, 2, '.', '') . '%' : 'N/A' }}
                    The chance your IRR goes negative, meaning you lose money overall. Set the threshold (< 10% probability of loss) to ensure profit isn't wiped out by bad scenarios. <strong>Lower is better.</strong>
                </li>
                <li>
                    <strong>Probability of IRR > 8%:</strong> {{ $note->simulationResults[0]->result['prob_above_8'] !== NULL ? number_format($note->simulationResults[0]->result['prob_above_8'] * 100, 2, '.', '') . '%' : 'N/A' }}
                    The likelyhood your IRR exceeds 8%, a common benchmark for other investments. <strong>Higher is better.</strong>
                </li>
            </ul>
            @endif
        </div>
    </section>
    <a href="{{ url('/notes') }}"><< Back to Notes</a>
</x-app-layout>
