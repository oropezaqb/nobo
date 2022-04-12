<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($header) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" style="overflow:auto;">

                    <?php $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                        ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
                        ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                        ->where('users.id', auth()->user()->id)
                        ->where('permissions.key', 'browse_queries')->exists(); ?>
                    @if ($authorized)

                        @if (!is_null($stmt))
                            <table border=1 cellpadding=5 cellspacing=0 style='border-collapse: collapse; border: 1px solid rgb(192, 192, 192);'>
                                <tr>
                                @foreach ($headings as $h)
                                    <th>{{ htmlspecialchars($h, ENT_QUOTES, 'UTF-8') }}
                                @endforeach
                                @while ($row = $stmt->fetch())
                                    <tr>
                                    @foreach ($row as $v)
                                        <td>{{ htmlspecialchars($v, ENT_QUOTES, 'UTF-8') }}
                                    @endforeach
                                @endwhile
                            </table>
                        @else
                            <p>The query returned an empty set.</p>
                        @endif

                    @else
                        You are not authorized to run queries.
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
