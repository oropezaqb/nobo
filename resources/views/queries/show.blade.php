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
                        ->where('permissions.key', 'read_queries')->exists(); ?>
                    @if ($authorized)
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
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
                                    required value="{!! old('title', $query->title) !!}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="category">Category: </label>
                                <input 
                                    class="form-control" 
                                    type="text" 
                                    name="category" 
                                    id="category"
                                    required
                                    value="{{ old('category', $query->category) }}"
                                    disabled>
                            </div>
                            <div class="form-group">
                                <label for="query">Query: </label>
                                <textarea class="form-control" rows="5" id="query" name="query" required disabled>{{ old('query', $query->query) }}</textarea>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="permission_id">Permission:&nbsp;</label>&nbsp;
                                <input list="permission_ids" id="permission_id0" onchange="setValue(this)" data-id="" class="custom-select" required disabled value="{!! old('permission_name', $query->permission->key) !!}">
                                <datalist id="permission_ids">
                                </datalist>
                                <input type="hidden" name="permission_id" id="permission_id0-hidden" value="{!! old('permission_id', $query->permission->id) !!}">
                                <input type="hidden" name="permission_name" id="name-permission_id0-hidden" value="{!! old('permission_name', $query->permission->key) !!}">
                            </div>
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                            <br>
                        </form>
                        <div style="clear: both;">
                            <div style="display: inline-block;">
                                <button class="btn btn-outline-primary" onclick="location.href = '/queries/{{ $query->id }}/edit';">Edit</button>
                            </div>
                            <div style="display: inline-block;">
                                <form method="POST" action="/queries/{{ $query->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                                </form>
                            </div>
                        </div>
                        <script>
                            function setValue (id) 
                            {
                                var input = id,
                                    list = input.getAttribute('list'),
                                    options = document.querySelectorAll('#' + list + ' option'),
                                    hiddenInput = document.getElementById(input.getAttribute('id') + '-hidden'),
                                    hiddenInputName = document.getElementById('name-' + input.getAttribute('id') + '-hidden'),
                                    label = input.value;
                                hiddenInputName.value = label;
                                hiddenInput.value = label;
                                for(var i = 0; i < options.length; i++) {
                                    var option = options[i];
                                    if(option.innerText === label) {
                                        hiddenInput.value = option.getAttribute('data-value');
                                        break;
                                    }
                                }
                            }
                        </script>
                    @else
                        You are not authorized to add queries.
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
