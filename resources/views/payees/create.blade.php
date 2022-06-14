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

                    <?php $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                        ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
                        ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                        ->where('users.id', auth()->user()->id)
                        ->where('permissions.key', 'add_payees')->exists(); ?>
                    @if ($authorized)
                        <form method="POST" action="/payees">
                            @csrf
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
                                    required value="{!! old('name') !!}">
                            </div>
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                            <button class="btn btn-outline-primary" type="submit">Save</button>
                        </form>
                        <br><br>
                        <form method="POST" action="/payees/upload" enctype="multipart/form-data">
                            @csrf
                            <h6 class="font-weight-bold">Import</h6>
                            <div class="form-group">
                                <label for="payees">Select a CSV file to upload (Payee Name), shall not contain commas</label>
                                <br>
                                {!! Form::file('payees') !!}
                                @error('payees')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button class="btn btn-outline-primary" type="submit">Import</button>
                        </form>
                    @else
                        You are not authorized to add payees.
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
