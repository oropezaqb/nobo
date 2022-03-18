@extends ('layouts.app')
@section ('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header font-weight-bold">Supplier Credits</div>
            <div class="card-body">
                <div id="wrapper">
                    <div
                        id="page"
                        class="container"
                    >
                        <h6 class="font-weight-bold">Search</h6>
                        <form method="GET" action="/payee">
                            @csrf
                            <div class="form-group">
                                <label for="payee_name">Payee Name: </label>
                                <input 
                                    class="form-control @error('payee_name') is-danger @enderror" 
                                    type="text" 
                                    name="payee_name" 
                                    id="payee_name" required
                                    value="{{ old('payee_name') }}">
                                @error('payee_name')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button class="btn btn-primary" type="submit">Search</button>
                        </form>
                        <p></p>
                        <h6 class="font-weight-bold">Add</h6>
                        <p>Want to record a new payee? Click <a href="{{ url('/payee/create') }}">here</a>!</p>
                        <p></p>
                        <h6 class="font-weight-bold">List</h6>
                        @forelse ($payees as $payee)
                            <div id="content">
                                <div id="title">
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '{{ $payee->path() }}';">View</button></div>
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '/payee/{{ $payee->id }}/edit';">Edit</button></div>
                                    <div style="display:inline-block;"><form method="POST" action="/payee/{{ $payee->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit">Delete</button>
                                    </form></div><div style="display:inline-block;">&nbsp;&nbsp;{{ $payee->name }}</div>
                                </div>
                            </div>
                        @empty
                            <p>No payees recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
