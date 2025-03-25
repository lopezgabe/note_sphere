<x-layout>
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
                            <a class="btn btn-warning float-end" href="{{ route('notes.export') }}"><i class="fa fa-download"></i> Export Note Data</a>
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
                    @foreach($notes as $note)
                        <tr>
                            <td>{{ $note->id }}</td>
                            <td>{{ $note->listing_price }}</td>
                            <td>{{ $note->upb_initial }}</td>
                            <td>{{ $note->monthly_pi }}</td>
                            <td>{{ $note->term_months }}</td>
                            <td>{{ $note->interest_rate }}</td>
                            <td>
                                <a href="/notes/{{ $note->id }}">
                                    Info
                                </a> |
                                <a href="/notes/run-simulation/{{ $note->id }}">
                                    Run Simulation
                                </a>
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
