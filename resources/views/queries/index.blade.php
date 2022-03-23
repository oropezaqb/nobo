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

                    <?php $browse = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                        ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
                        ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                        ->where('users.id', auth()->user()->id)
                        ->where('permissions.key', 'browse_queries')->exists(); ?>
                    @if ($browse)
                        <h6 class="font-weight-bold">Search</h6>
                        <form method="GET" action="/queries">
                            @csrf
                            <div class="form-group">
                                <label for="title">Title: </label>
                                <input
                                    class="form-control @error('title') is-danger @enderror"
                                    type="text"
                                    name="title"
                                    id="title" required
                                    value="{{ old('title') }}">
                                @error('query_title')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button class="btn btn-outline-primary" type="submit">Search</button>
                        </form>
                        <br>
                        <h6 class="font-weight-bold">Add</h6>
                        <p>Want to record a new query? Click <a class="text-primary" href="{{ url('/queries/create') }}">here</a>!</p>
                        <br>
                        <h6 class="font-weight-bold">List</h6>
                        @forelse ($queries as $query)
                            <div id="content">
                                <div id="title">
                                    <div style="display:inline-block;"><form method="POST" action="/queries/{{ $query->id }}/run">
                                        @csrf
                                        <button class="btn btn-link" type="submit">Run</button>
                                    </form></div>
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '{{ $query->path() }}';">View</button></div>
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '/queries/{{ $query->id }}/edit';">Edit</button></div>
                                    <div style="display:inline-block;"><form method="POST" action="/queries/{{ $query->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit">Delete</button>
                                    </form></div><div style="display:inline-block;">&nbsp;&nbsp;{{ $query->title }}</div>
                                </div>
                            </div>
                        @empty
                            <p>No queries recorded yet.</p>
                        @endforelse
                    @else
                        You are not authorized to browse queries.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
