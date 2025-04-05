<x-layout>
    <x-slot:heading>
        Simulation Result
    </x-slot:heading>


    <h1>What-If Scenarios for Note #{{ $note->id }}</h1>
    <p class="{{ $note->is_favorite ? 'alert alert-success' : ($note->is_avoid ? 'alert alert-danger' : '') }}"><strong>Note
            ID:</strong> {{ $note->note_id }}
        <strong>Note Link:</strong> <a href="{{ $note->url }}">{{ $note->url }}</a></p>

    <!-- Favorite Toggle -->
    <form action="{{ route('notes.favorite', $note->id) }}" method="POST"
          style="display:inline;">
        @csrf
        <button type="submit"
                class="btn btn-sm {{ $note->is_favorite ? 'btn-warning' : 'btn-outline-warning' }}">
            <i class="fa fa-star"></i> {{ $note->is_favorite ? 'Unfavorite' : 'Favorite' }}
        </button>
    </form>

    <!-- Avoid Toggle -->
    <form action="{{ route('notes.avoid', $note->id) }}" method="POST"
          style="display:inline;">
        @csrf
        <button type="submit"
                class="btn btn-sm {{ $note->is_avoid ? 'btn-danger' : 'btn-outline-danger' }}">
            <i class="fa fa-ban"></i> {{ $note->is_avoid ? 'Unavoid' : 'Avoid' }}
        </button>
    </form>

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

            @if (!empty($scenario->example_scenarios))
                <h5>Example Scenarios to Try: Discounts</h5>
                <ul>
                    <li>Purchase Price@Discount: listing_price - (listing_price * %)</li>
                    @foreach ($scenario->example_scenarios as $key => $discount)
                        <li>{{ $key }}%: {{ $discount }}</li>
                    @endforeach
                </ul>
            @endif

            <h3>{{ $scenario->name }} (Run at {{ $scenario->completed_at->toDateTimeString() }})</h3>
            <p><strong>Parameters:</strong></p>
            <pre>{{ json_encode($scenario->parameters, JSON_PRETTY_PRINT) }}</pre>
            <h4>Raw Result</h4>
            <pre>{{ json_encode($scenario->result, JSON_PRETTY_PRINT) }}</pre>

            <h4>Base Metrics</h4>
            <ul>
                <li>ITV: {{ $note->getNumberPercent($scenario->result['itv']) }}</li>
                <li>Purchase Price: {{ $note->getNumberCurrency($scenario->result['pv']) }}</li>
                <li>Monthly Payment: {{ $note->getNumberCurrency($scenario->result['monthly_pi']) }}</li>
                <li>Baseline IRR: {{ $note->getNumberPercent($scenario->result['baseline_irr']) }}</li>
            </ul>

            <h4 class="text-2xl font-bold mb-4">Simulation Result Analysis</h4>
            @if (!empty($scenario->analysis))
                <ul>
                    @if (!empty($scenario->analysis['recoup']['value']))
                        <li>Recoup (↓): {{ $note->getNumber($scenario->analysis['recoup']['value']) }} months
                            <span
                                class="{{ $scenario->analysis['recoup']['is_low'] ? 'bg-success text-white' : 'bg-danger text-white' }} p-1 rounded"
                                data-bs-toggle="tooltip" title="Months for recoup investment">
                            <i class="fa {{ $scenario->analysis['recoup']['is_low'] ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $scenario->analysis['recoup']['is_low'] ? 'Low (Good)' : 'Not Low' }}
                        </span>
                            <p>{{ $scenario->analysis['recoup']['description'] ?? '' }}</p>
                        </li>
                    @endif

                    <li>Mean IRR (↑): {{ $note->getNumberPercent($scenario->analysis['mean_irr']['value']) }}
                        <span
                            class="{{ $scenario->analysis['mean_irr']['is_high'] ? 'bg-success text-white' : 'bg-danger text-white' }} p-1 rounded"
                            data-bs-toggle="tooltip" title="High IRR indicates strong returns">
                            <i class="fa {{ $scenario->analysis['mean_irr']['is_high'] ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $scenario->analysis['mean_irr']['is_high'] ? 'High (Good)' : 'Not High' }}
                        </span>
                        <p>{{ $scenario->analysis['mean_irr']['description'] ?? '' }}</p>
                    </li>
                    <li>Median IRR vs Mean (↑):
                        Median {{ $note->getNumberPercent($scenario->analysis['median_vs_mean']['median']) }}vs
                        Mean {{ $note->getNumberPercent($scenario->analysis['median_vs_mean']['mean']) }}
                        <span
                            class="{{ $scenario->analysis['median_vs_mean']['is_median_less'] ? 'bg-success text-white' : 'bg-danger text-white' }} p-1 rounded"
                            data-bs-toggle="tooltip" title="High IRR indicates strong returns">
                            <i class="fa {{ $scenario->analysis['median_vs_mean']['is_median_less'] ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $scenario->analysis['median_vs_mean']['is_median_less'] ? 'High (Good)' : 'Not High' }}
                        </span>

                        <p>{{ $scenario->analysis['median_vs_mean']['description'] ?? '' }}</p>
                    </li>
                    <li>Standard Deviation (↓): {{ $note->getNumberPercent($scenario->analysis['std_irr']['value']) }}
                        <span
                            class="{{ $scenario->analysis['std_irr']['is_low'] ? 'bg-success text-white' : 'bg-danger text-white' }} p-1 rounded"
                            data-bs-toggle="tooltip" title="Lower means stability">
                            <i class="fa {{ $scenario->analysis['std_irr']['is_low'] ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $scenario->analysis['std_irr']['is_low'] ? 'Low (Good)' : 'Not Low' }}
                        </span>
                        <p>{{ $scenario->analysis['std_irr']['description'] ?? '' }}</p>
                    </li>
                    <li>5th Percentile (↑): {{ $note->getNumberPercent($scenario->analysis['percentile_5']['value']) }}
                        <span
                            class="{{ $scenario->analysis['percentile_5']['is_positive'] ? 'bg-success text-white' : 'bg-danger text-white' }} p-1 rounded"
                            data-bs-toggle="tooltip" title="Higher the better">
                            <i class="fa {{ $scenario->analysis['percentile_5']['is_positive'] ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $scenario->analysis['percentile_5']['is_positive'] ? 'Positive (Green Light)' : 'Not Positive' }}
                        </span>

                        <p>{{ $scenario->analysis['percentile_5']['description'] ?? '' }}</p>
                    </li>
                    <li>95th Percentile
                        (↑): {{ $note->getNumberPercent($scenario->analysis['percentile_95']['value']) }}
                        <span
                            class="{{ $scenario->analysis['percentile_95']['is_high'] ? 'bg-success text-white' : 'bg-danger text-white' }} p-1 rounded"
                            data-bs-toggle="tooltip" title="Higher the better">
                            <i class="fa {{ $scenario->analysis['percentile_95']['is_high'] ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $scenario->analysis['percentile_95']['is_high'] ? 'High (Good)' : 'Not High' }}
                        </span>
                        <p>{{ $scenario->analysis['percentile_95']['description'] ?? '' }}</p>
                    </li>
                    <li>Probability of Loss
                        (↓): {{ $note->getNumberPercent($scenario->analysis['prob_loss']['value']) }}
                        <span
                            class="{{ $scenario->analysis['prob_loss']['is_low'] ? 'bg-success text-white' : 'bg-danger text-white' }} p-1 rounded"
                            data-bs-toggle="tooltip" title="Lower the better">
                            <i class="fa {{ $scenario->analysis['prob_loss']['is_low'] ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $scenario->analysis['prob_loss']['is_low'] ? 'Low (Good)' : 'Not Low' }}
                        </span>
                        <p>{{ $scenario->analysis['prob_loss']['description'] ?? '' }}</p>
                    </li>
                    <li>Probability of IRR > 8%
                        (↑): {{ $note->getNumberPercent($scenario->analysis['prob_above_8']['value']) }}
                        <span
                            class="{{ $scenario->analysis['prob_above_8']['is_high'] ? 'bg-success text-white' : 'bg-danger text-white' }} p-1 rounded"
                            data-bs-toggle="tooltip" title="Higher the better">
                            <i class="fa {{ $scenario->analysis['prob_above_8']['is_high'] ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $scenario->analysis['prob_above_8']['is_high'] ? 'High (Good)' : 'Not High' }}
                        </span>
                        <p>{{ $scenario->analysis['prob_above_8']['description'] ?? '' }}</p>
                    </li>
                </ul>

                <form action="{{ url('/notes/simulation/' . $scenario->id . '/delete') }}" method="POST"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Delete this scenario?')">Delete
                    </button>
                </form>

                <h5>Notes</h5>
                @if (!empty($scenario->notes))
                    {{ $scenario->notes }}
                @else
                    <p>Missing Notes.</p>
                @endif

            @else
                <p>No analysis yet.</p>
            @endif
        @endforeach
    @else
        <p>No scenarios yet. Add one above.</p>
    @endif

    @if (!empty($scenarios) && $scenarios->isNotEmpty())
        <h2 class="text-2xl font-bold mb-4">Scenario Comparison</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Recoup (↓)</th>
                <th>Mean IRR (↑)</th>
                <th>Median IRR (↑)</th>
                <th>Std Dev (↓)</th>
                <th>5th Percentile (↑)</th>
                <th>95th Percentile (↑)</th>
                <th>Prob Loss (↓)</th>
                <th>Prob > 8% (↑)</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($scenarios as $scenario)
                @if (!empty($scenario->analysis))
                    <tr>
                        <td>{{ $scenario->name }}</td>
                        <td>{{ $note->getNumber($scenario->analysis['recoup']['value']) }} months</td>
                        <td>{{ $note->getNumberPercent($scenario->analysis['mean_irr']['value']) }}</td>
                        <td>{{ $note->getNumberPercent($scenario->analysis['median_vs_mean']['median']) }}</td>
                        <td>{{ $note->getNumberPercent($scenario->analysis['std_irr']['value']) }}</td>
                        <td>{{ $note->getNumberPercent($scenario->analysis['percentile_5']['value']) }}</td>
                        <td>{{ $note->getNumberPercent($scenario->analysis['percentile_95']['value']) }}</td>
                        <td>{{ $note->getNumberPercent($scenario->analysis['prob_loss']['value']) }}</td>
                        <td>{{ $note->getNumberPercent($scenario->analysis['prob_above_8']['value']) }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    @endif

    <p><a href="{{ url('/notes') }}">Back to Notes</a></p>
</x-layout>
