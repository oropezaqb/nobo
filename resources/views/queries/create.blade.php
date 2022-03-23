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
                        ->where('permissions.key', 'add_queries')->exists(); ?>
                    @if ($authorized)
                        <form method="POST" action="/queries">
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
                                <label for="title">Title</label>&nbsp;
                                <input type="text" class="form-control" id="title" name="title" style="text-align: left;"
                                    required value="{!! old('title') !!}">
                            </div>
                            <div class="form-group">
                                <label for="category">Category: </label>
                                <input 
                                    class="form-control" 
                                    type="text" 
                                    name="category" 
                                    id="category"
                                    required
                                    value="{{ old('category') }}">
                            </div>
                            <div class="form-group">
                                <label for="query">Query: </label>
                                <textarea class="form-control" rows="5" id="query" name="query" required>{{ old('query') }}</textarea>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="permission_id">Permission:&nbsp;</label>&nbsp;
                                <input list="permission_ids" id="permission_id0" onchange="setValue(this)" data-id="" class="custom-select" required value="{!! old('permission_name') !!}">
                                <datalist id="permission_ids">
                                    @foreach ($permissions as $permission)
                                        <option data-value="{{ $permission->id }}">{{ $permission->key }}</option>
                                    @endforeach
                                </datalist>
                                <input type="hidden" name="permission_id" id="permission_id0-hidden" value="{!! old('permission_id') !!}">
                                <input type="hidden" name="permission_name" id="name-permission_id0-hidden" value="{!! old('permission_name') !!}">
                            </div>
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                            <br>
                            <button class="btn btn-outline-primary" type="submit">Save</button>
                        </form>
                    @else
                        You are not authorized to add queries.
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
