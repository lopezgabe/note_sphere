<x-layout>
    <x-slot:heading>
        Simulation Result
    </x-slot:heading>

    <h1>What-If Scenarios for Note #{{ $note->id }}</h1>
    <p><strong>Note ID:</strong> {{ $note->note_id }}</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
                const button = this.querySelector('button[type="submit"]');
                button.disabled = true;
                button.innerHTML = 'Running... <i class="fa fa-spinner fa-spin"></i>';
            });
        });
    </script>

    <h2>Add a New Scenario</h2>
    <form action="{{ url('/notes/run-simulation/' . $note->id) }}" method="POST">
        @csrf
        <div class="col-md-6">
            <label for="name">Scenario Name:</label>
            <input type="text" name="name" id="name" placeholder="e.g., Base Case">
        </div>
        <div class="col-md-6">
            <label for="simulations">Number of Simulations:</label>
            <input type="number" name="parameters[simulations]" id="simulations" value="10000" min="1">
        </div>
        <div class="col-md-6">
            <label for="purchase_price">Purchase Price:</label>
            <input type="number" name="parameters[purchase_price]" id="purchase_price"
                   value="{{ $note->getRawOriginal('listing_price') }}" step="0.01">
        </div>
        <div class="col-md-6">
            <label for="upb_initial">Initial UPB:</label>
            <input type="number" name="parameters[upb_initial]" id="upb_initial"
                   value="{{ $note->getRawOriginal('upb_initial') }}" step="0.01">
        </div>
        <div class="col-md-6">
            <label for="monthly_pi">Monthly P&I:</label>
            <input type="number" name="parameters[monthly_pi]" id="monthly_pi"
                   value="{{ $note->getRawOriginal('monthly_pi') }}" step="0.01">
        </div>
        <div class="col-md-6">
            <label for="term_months">Term (Months):</label>
            <input type="number" name="parameters[term_months]" id="term_months"
                   value="{{ $note->getRawOriginal('term_months') }}" min="1">
        </div>
        <div class="col-md-6">
            <label for="interest_rate">Interest Rate (%):</label>
            <input type="number" name="parameters[interest_rate]" id="interest_rate"
                   value="{{ $note->getRawOriginal('interest_rate') }}" step="0.01">
        </div>
        <div class="col-md-12">
            <button type="submit">Run Scenario</button>
        </div>
    </form>

    <h2>Existing Scenarios</h2>

    @if (empty($result) && isset($result['error']))
        <p style="color: red;">{{ $result['error'] }}</p>
    @endif

    @if ($scenarios->isNotEmpty())
        @foreach ($scenarios as $scenario)

            <h3>{{ $scenario->name }} (Run at {{ $scenario->completed_at->toDateTimeString() }})</h3>
            <p><strong>Parameters:</strong></p>
            <pre>{{ json_encode($scenario->parameters, JSON_PRETTY_PRINT) }}</pre>
            <h4>Raw Result</h4>
            <pre>{{ json_encode($scenario->result, JSON_PRETTY_PRINT) }}</pre>

            <h4>Base Metrics</h4>
            <ul>
                <li>ITV: {{ $note->getNumberPercent($scenario->result['itv']) }}</li>
                <li>Present Value: {{ $note->getNumberCurrency($scenario->result['pv']) }}</li>
                <li>Baseline IRR: {{ $note->getNumberPercent($scenario->result['baseline_irr']) }}</li>
            </ul>


            <h4>Analysis</h4>
            @if (!empty($scenario->analysis))
                <ul>
                    <li>Mean IRR: {{ $note->getNumberPercent($scenario->analysis['mean_irr']['value']) }}
                        ({{ $scenario->analysis['mean_irr']['is_high'] ? 'High (Good)' : 'Not High' }})
                    </li>
                    <li>Median IRR vs Mean:
                        Median {{ $note->getNumberPercent($scenario->analysis['median_vs_mean']['median']) }}
                        vs Mean {{ $note->getNumberPercent($scenario->analysis['median_vs_mean']['mean']) }}
                        ({{ $scenario->analysis['median_vs_mean']['is_median_less'] ? 'Median < Mean (Good)' : 'Median >= Mean' }}
                        )
                    </li>
                    <li>Standard Deviation: {{ $note->getNumberPercent($scenario->analysis['std_irr']['value']) }}
                        ({{ $scenario->analysis['std_irr']['is_low'] ? 'Low (Good)' : 'Not Low' }})
                    </li>
                    <li>5th Percentile: {{ $note->getNumberPercent($scenario->analysis['percentile_5']['value']) }}
                        ({{ $scenario->analysis['percentile_5']['is_positive'] ? 'Positive (Green Light)' : 'Not Positive' }}
                        )
                    </li>
                    <li>95th Percentile: {{ $note->getNumberPercent($scenario->analysis['percentile_95']['value']) }}
                        ({{ $scenario->analysis['percentile_95']['is_high'] ? 'High (Good)' : 'Not High' }})
                    </li>
                    <li>Probability of Loss: {{ $note->getNumberPercent($scenario->analysis['prob_loss']['value']) }}
                        ({{ $scenario->analysis['prob_loss']['is_low'] ? 'Low (Good)' : 'Not Low' }})
                    </li>
                    <li>Probability of IRR >
                        8%: {{ $note->getNumberPercent($scenario->analysis['prob_above_8']['value']) }}
                        ({{ $scenario->analysis['prob_above_8']['is_high'] ? 'High (Good)' : 'Not High' }})
                    </li>
                </ul>

                <form action="{{ url('/notes/simulation/' . $scenario->id . '/delete') }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this scenario?')">Delete</button>
                </form>
            @else
                <p>No analysis yet.</p>
            @endif
        @endforeach
    @else
        <p>No scenarios yet. Add one above.</p>
    @endif

    @if ($scenarios->isNotEmpty())
        <h2>Scenario Comparison</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Mean IRR</th>
                <th>Median IRR</th>
                <th>Std Dev</th>
                <th>5th Percentile</th>
                <th>95th Percentile</th>
                <th>Prob Loss</th>
                <th>Prob > 8%</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($scenarios as $scenario)
                <tr>
                    <td>{{ $scenario->name }}</td>
                    <td>{{ $scenario->analysis['mean_irr']['value'] }}</td>
                    <td>{{ $scenario->analysis['median_vs_mean']['median'] }}</td>
                    <td>{{ $scenario->analysis['std_irr']['value'] }}</td>
                    <td>{{ $scenario->analysis['percentile_5']['value'] }}</td>
                    <td>{{ $scenario->analysis['percentile_95']['value'] }}</td>
                    <td>{{ $scenario->analysis['prob_loss']['value'] }}</td>
                    <td>{{ $scenario->analysis['prob_above_8']['value'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <p><a href="{{ url('/notes') }}">Back to Notes</a></p>
</x-layout>
