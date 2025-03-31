<x-layout>
    <style>
        th a.active {
            font-weight: bold;
            color: #007bff;
        }
    </style>

    <script>
        document.querySelectorAll('.toggle-btn').forEach(button => {

            button.addEventListener('click', function (e) {
                console.log('Made it');
                e.preventDefault();
                const form = this.closest('form');
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: new FormData(form)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.toggle('btn-warning', data.is_favorite);
                            this.classList.toggle('btn-outline-warning', !data.is_favorite);
                            this.textContent = data.is_favorite ? '★ Unfavorite' : '★ Favorite';
                        }
                    });
            });
        });
    </script>

    <x-slot:heading>
        Note Listings
    </x-slot:heading>

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

                <section>
                    <form action="{{ route('notes.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" class="form-control">
                        <br>
                        <button class="btn btn-success"><i class="fa fa-file"></i> Import Note Data</button>
                    </form>
                </section>
                <hr>
                <div class="mb-3">
                    <a href="{{ url('/notes?favorite=1') }}" class="btn btn-success toggle-btn">Show Favorites</a>
                    <a href="{{ url('/notes?avoid=1') }}" class="btn btn-danger toggle-btn">Show Avoided</a>
                    <a href="{{ url('/notes') }}" class="btn btn-secondary toggle-btn">Show All</a>
                </div>

                <table class="table table-bordered mt-3">
                    <tr>
                        <th colspan="8">
                            List Of Notes
                            <a class="btn btn-warning float-end" href="{{ route('notes.export') }}"><i
                                    class="fa fa-download"></i> Export Note Data</a>
                        </th>
                    </tr>
                    <tr>
                        <th>
                            <a href="{{ url('/notes?sort=id&direction=' . (request('sort') === 'id' && request('direction') === 'asc' ? 'desc' : 'asc') . (request('favorite') ? '&favorite=1' : '') . (request('avoid') ? '&avoid=1' : '')) }}">
                                ID
                            </a>
                            @if (request('sort') === 'id')
                                {{ request('direction') === 'asc' ? ' ↑' : ' ↓' }}
                            @endif
                        </th>
                        <th>
                            <a href="{{ url('/notes?sort=note_id&direction=' . (request('sort') === 'note_id' && request('direction') === 'asc' ? 'desc' : 'asc') . (request('favorite') ? '&favorite=1' : '') . (request('avoid') ? '&avoid=1' : '')) }}">
                                Note ID
                            </a>
                            @if (request('sort') === 'note_id')
                                {{ request('direction') === 'asc' ? ' ↑' : ' ↓' }}
                            @endif
                        </th>
                        <th>
                            <a href="{{ url('/notes?sort=listing_price&direction=' . (request('sort') === 'listing_price' && request('direction') === 'asc' ? 'desc' : 'asc') . (request('favorite') ? '&favorite=1' : '') . (request('avoid') ? '&avoid=1' : '')) }}">
                                Listing Price
                            </a>
                            @if (request('sort') === 'listing_price')
                                {{ request('direction') === 'asc' ? ' ↑' : ' ↓' }}
                            @endif
                        </th>
                        <th>
                            <a href="{{ url('/notes?sort=upb_initial&direction=' . (request('sort') === 'upb_initial' && request('direction') === 'asc' ? 'desc' : 'asc') . (request('favorite') ? '&favorite=1' : '') . (request('avoid') ? '&avoid=1' : '')) }}">
                                UnPaid Balance
                            </a>
                            @if (request('sort') === 'upb_initial')
                                {{ request('direction') === 'asc' ? ' ↑' : ' ↓' }}
                            @endif
                        </th>
                        <th>
                            <a href="{{ url('/notes?sort=monthly_pi&direction=' . (request('sort') === 'monthly_pi' && request('direction') === 'asc' ? 'desc' : 'asc') . (request('favorite') ? '&favorite=1' : '') . (request('avoid') ? '&avoid=1' : '')) }}">
                                Monthly Payment
                            </a>
                            @if (request('sort') === 'monthly_pi')
                                {{ request('direction') === 'asc' ? ' ↑' : ' ↓' }}
                            @endif
                        </th>
                        <th>
                            <a href="{{ url('/notes?sort=term_months&direction=' . (request('sort') === 'term_months' && request('direction') === 'asc' ? 'desc' : 'asc') . (request('favorite') ? '&favorite=1' : '') . (request('avoid') ? '&avoid=1' : '')) }}">
                                Term Months
                            </a>
                            @if (request('sort') === 'term_months')
                                {{ request('direction') === 'asc' ? ' ↑' : ' ↓' }}
                            @endif
                        </th>
                        <th>
                            <a href="{{ url('/notes?sort=interest_rate&direction=' . (request('sort') === 'interest_rate' && request('direction') === 'asc' ? 'desc' : 'asc') . (request('favorite') ? '&favorite=1' : '') . (request('avoid') ? '&avoid=1' : '')) }}">
                                Interest Rate
                            </a>
                            @if (request('sort') === 'interest_rate')
                                {{ request('direction') === 'asc' ? ' ↑' : ' ↓' }}
                            @endif
                        </th>
                        <th>Actions</th>
                    </tr>
                    @foreach ($notes as $key => $note)
                        <tr class="{{ $note->is_favorite ? 'table-success' : ($note->is_avoid ? 'table-danger' : '') }}">
                            <td>{{ $note->id }}</td>
                            <td>{{ $note->note_id }}</td>
                            <td>{{ $note->listing_price }}</td>
                            <td>{{ $note->upb_initial }}</td>
                            <td>{{ $note->monthly_pi }}</td>
                            <td>{{ $note->term_months }}</td>
                            <td>{{ $note->interest_rate }}</td>
                            <td>
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
                                        <button type="submit" class="btn btn-primary">Run Scenario</button>
                                    </div>
                                </form>

                                <!-- Link to view results -->
                                <a href="{{ url('/notes/run-simulation/' . $note->id) }}">View Scenarios</a>

                                <form action="{{ route('notes.update-notes', $note->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <label for="notes_{{ $note->id }}">Notes:</label>
                                    <textarea name="notes" id="notes_{{ $note->id }}" class="form-control" rows="2" placeholder="Add your notes here...">{{ $note->notes }}</textarea>
                                    <button type="submit" class="btn btn-secondary btn-sm mt-1">Save Notes</button>
                                </form>

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

    <script>
        document.querySelectorAll('.simulation-form').forEach(form => {
            form.addEventListener('submit', function() {
                const button = this.querySelector('button[type="submit"]');
                const spinner = button.querySelector('.spinner');
                const text = button.querySelector('.text');
                button.disabled = true;
                spinner.style.display = 'inline';
                text.textContent = 'Running...';
            });
        });
    </script>

</x-layout>
