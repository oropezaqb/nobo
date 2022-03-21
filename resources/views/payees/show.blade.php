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
                        ->where('permissions.key', 'read_payees')->exists(); ?>
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
                                    required value="{!! old('name', $payee->name) !!}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="user">User</label>&nbsp;
                                <input type="text" class="form-control" id="user" name="user" style="text-align: left;"
                                    required value="{!! $payee->user->name !!}" disabled>
                            </div>
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                        </form>

                        <div style="clear: both;">
                            <div style="display: inline-block;">
                                <button class="btn btn-outline-primary" onclick="location.href = '/payees/{{ $payee->id }}/edit';">Edit</button>
                            </div>
                            <div style="display: inline-block;">
                                <form method="POST" action="/payees/{{ $payee->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    @else
                        You are not authorized to browse payee details.
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
