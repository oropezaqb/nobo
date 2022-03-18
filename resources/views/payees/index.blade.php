<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($header) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

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
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </form>
                    <br>
                    <h6 class="font-weight-bold">Add</h6>
                    <p>Want to record a new payee? Click <a class="text-primary" href="{{ url('/payees/create') }}">here</a>!</p>
                    <br>
                    <h6 class="font-weight-bold">List</h6>
                    @forelse ($payees as $payee)
                        <div id="content">
                            <div id="title">
                                <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '{{ $payee->path() }}';">View</button></div>
                                <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '/payees/{{ $payee->id }}/edit';">Edit</button></div>
                                <div style="display:inline-block;"><form method="POST" action="/payees/{{ $payee->id }}">
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
</x-app-layout>
