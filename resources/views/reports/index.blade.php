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
                         @if (session('status'))
                             <div class="alert alert-success" role="alert">
                                 {{ session('status') }}
                             </div>
                        @endif
                        <h6 class="font-weight-bold">Reports</h6>
                        <br>
                        <div id="content">
                            <div id="title">
                                <a class="text-primary" href="{{ url('/reports/per-supplier') }}">Bills per supplier</a>
                                <br><br>
                            </div>
                        </div>
                        <h6 class="font-weight-bold">Queries</h6>
                        @forelse ($queries as $query)
                            <div id="content">
                                <div id="title">
                                    <div style="display:inline-block;"><form method="POST" action="/queries/{{ $query->id }}/run">
                                        @csrf
                                        <button class="btn btn-link" type="submit">Run</button>
                                    </form></div>
                                    <div style="display:inline-block;"><form method="POST" action="/queries/{{ $query->id }}/csv">
                                        @csrf
                                        <button class="btn btn-link" type="submit">CSV</button>
                                    </form></div>
                                    <div style="display:inline-block;">&nbsp;&nbsp;{{ $query->title }}</div>
                                </div>
                            </div>
                        @empty
                            <p>No queries recorded yet.</p>
                        @endforelse
                        {{ $queries->links() }}
                    @else
                        You are not authorized to browse queries and reports.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
