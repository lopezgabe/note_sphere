<x-layout>
    <x-slot:heading>
        Simulation Result
    </x-slot:heading>

    @if ($note->simulationResults->isNotEmpty())
        <h3>{{ $note->note_id }}</h3>
        <ul>
            <li><strong>Simulations</strong> {{ $note->simulationResults[0]->result['simulations'] }}</li>
            <li><strong>Purchase Price</strong> {{ $note->listing_price }}</li>
            <li><strong>UPB Initial</strong> {{ $note->upb_initial }}</li>
            <li><strong>Monthly PI</strong> {{ $note->monthly_pi }}</li>
            <li><strong>Term Months</strong> {{ $note->term_months }}</li>
            <li><strong>Baseline IRR</strong> {{ $note->simulationResults[0]->result['baseline_irr'] !== NULL ? number_format($note->simulationResults[0]->result['baseline_irr'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
            <li><strong>Interest Rate</strong> {{ $note->interest_rate }}</li>
            <li><strong>ITV</strong> {{ $note->simulationResults[0]->result['itv'] !== NULL ? number_format($note->simulationResults[0]->result['itv'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
            <li><strong>PV</strong> {{ $note->simulationResults[0]->result['pv'] !== NULL ? '$' . number_format($note->simulationResults[0]->result['pv']/100,2, '.', ',') : 'N/A' }}</li>

<hr>
            <li><strong>Average IRR</strong> {{ $note->simulationResults[0]->result['mean_irr'] !== NULL ? number_format($note->simulationResults[0]->result['mean_irr'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
            <li><strong>Median IRR</strong> {{ $note->simulationResults[0]->result['median_irr'] !== NULL ? number_format($note->simulationResults[0]->result['median_irr'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
            <li><strong>Standard Deviation</strong> {{ $note->simulationResults[0]->result['std_irr'] !== NULL ? number_format($note->simulationResults[0]->result['std_irr'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
            <li><strong>5th Percentile IRR</strong> {{ $note->simulationResults[0]->result['percentile_5'] !== NULL ? number_format($note->simulationResults[0]->result['percentile_5'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
            <li><strong>95th Percentile IRR</strong> {{ $note->simulationResults[0]->result['percentile_95'] !== NULL ? number_format($note->simulationResults[0]->result['percentile_95'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
            <li><strong>Probability of Loss</strong> {{ $note->simulationResults[0]->result['prob_loss'] !== NULL ? number_format($note->simulationResults[0]->result['prob_loss'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
            <li><strong>Probability of IRR > 8%</strong> {{ $note->simulationResults[0]->result['prob_above_8'] !== NULL ? number_format($note->simulationResults[0]->result['prob_above_8'] * 100, 2, '.', '') . '%' : 'N/A' }}</li>
{{--<hr>--}}
{{--            @foreach ($note->simulationResults[0]->result as $key => $simulationResult)--}}
{{--                <li><strong>{{ $key }}:</strong> {{ $simulationResult }}</li>--}}
{{--            @endforeach--}}
{{--            {{ dd($note) }}--}}
{{--            @foreach ($note as $key => $item)--}}
{{--                {{ dd($item) }}--}}
{{--                <li><strong>{{ $key }}:</strong> {{ $item }}</li>--}}
{{--            @endforeach--}}
        </ul>
    @elseif (isset($result['message']))
        <p>{{ $result['message'] }}</p>
    @elseif (isset($result['error']))
        <p style="color: red;">{{ $result['error'] }}</p>
    @elseif (isset($result))
        <pre>{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
    @else
        <p>No simulation results yet.</p>
    @endif

    <a href="{{ url('/notes') }}">Back to Notes</a>
</x-layout>
