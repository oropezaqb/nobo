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

                    <form method="POST" action="/payees/{{ $payee->id }}">
                        @csrf
                        @method('PUT')
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="name">Name</label>&nbsp;
                            <input type="text" class="form-control" id="name" name="name" style="text-align: left;"
                                required value="{!! old('name', $payee->name) !!}">
                        </div>
                        <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                        <button class="btn btn-outline-primary" type="submit">Save</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
