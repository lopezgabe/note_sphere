<x-layout>
    <x-slot:heading>
        Note Listings
    </x-slot:heading>

    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const button = this.querySelector('button[type="submit"]');
                button.disabled = true;
                button.innerHTML = 'Running... <i class="fa fa-spinner fa-spin"></i>';
            });
        });
    </script>

    <div class="container">
        <div class="card mt-5">
            <h3 class="card-header p-3"><i class="fa fa-star"></i> Import CSV to Database</h3>
            <div class="card-body">

                @session('success')
                <div class="alert alert-success" role="alert">
                    {{ $value }}
                </div>
                @endsession

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('notes.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control">
                    <br>
                    <button class="btn btn-success"><i class="fa fa-file"></i> Import Note Data</button>
                </form>

                <table class="table table-bordered mt-3">
                    <tr>
                        <th colspan="7">
                            List Of Notes
                            <a class="btn btn-warning float-end" href="{{ route('notes.export') }}"><i
                                    class="fa fa-download"></i> Export Note Data</a>
                        </th>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>Listing Price</th>
                        <th>UnPaid Balance</th>
                        <th>Monthly Payment</th>
                        <th>Term Months</th>
                        <th>Interest Rate</th>
                        <th>Actions</th>
                    </tr>
                    @foreach ($notes as $key => $note)
                        <tr>
                            <td>{{ $note->note_id }}</td>
                            <td>{{ $note->listing_price }}</td>
                            <td>{{ $note->upb_initial }}</td>
                            <td>{{ $note->monthly_pi }}</td>
                            <td>{{ $note->term_months }}</td>
                            <td>{{ $note->interest_rate }}</td>
                            <td>
                                <!-- Form for new what-if scenario -->
                                <form action="{{ url('/notes/run-simulation/' . $note->id) }}" method="POST">
                                    @csrf
                                    <div class="col-md-6">
                                        <label for="name_{{ $note->id }}">Scenario Name:</label>
                                        <input type="text" name="name" id="name_{{ $note->id }}"
                                               placeholder="e.g., Base Case">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="simulations_{{ $note->id }}">Simulations:</label>
                                        <input type="number" name="parameters[simulations]"
                                               id="simulations_{{ $note->id }}" value="10000" min="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="purchase_price_{{ $note->id }}">Purchase Price:</label>
                                        <input type="number" name="parameters[purchase_price]"
                                               id="purchase_price_{{ $note->id }}"
                                               value="{{ $note->getRawOriginal('listing_price') }}" step="0.01">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="upb_initial_{{ $note->id }}">Initial UPB:</label>
                                        <input type="number" name="parameters[upb_initial]"
                                               id="upb_initial_{{ $note->id }}"
                                               value="{{ $note->getRawOriginal('upb_initial') }}" step="0.01">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="monthly_pi_{{ $note->id }}">Monthly P&I:</label>
                                        <input type="number" name="parameters[monthly_pi]"
                                               id="monthly_pi_{{ $note->id }}"
                                               value="{{ $note->getRawOriginal('monthly_pi') }}" step="0.01">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="term_months_{{ $note->id }}">Term (Months):</label>
                                        <input type="number" name="parameters[term_months]"
                                               id="term_months_{{ $note->id }}"
                                               value="{{ $note->getRawOriginal('term_months') }}" min="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="interest_rate_{{ $note->id }}">Interest Rate (%):</label>
                                        <input type="number" name="parameters[interest_rate]"
                                               id="interest_rate_{{ $note->id }}"
                                               value="{{ $note->getRawOriginal('interest_rate') }}" step="0.01">
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit">Run Scenario</button>
                                    </div>
                                </form>

                                <!-- Link to view results -->
                                <a href="{{ url('/notes/run-simulation/' . $note->id) }}">View Scenarios</a>
                            </td>
                        </tr>
                    @endforeach
                </table>

            </div>
        </div>

        <div>
            {{ $notes->links() }}
        </div>
    </div>
</x-layout>
