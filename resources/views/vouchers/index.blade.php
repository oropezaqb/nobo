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
                        ->where('permissions.key', 'browse_vouchers')->exists(); ?>
                    @if ($browse)
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <h6 class="font-weight-bold">Search</h6>
                        <form method="GET" action="/vouchers">
                            @csrf
                            <div class="form-group">
                                <label for="number">Number: </label>
                                <input
                                    class="form-control @error('number') is-danger @enderror"
                                    type="text"
                                    name="number"
                                    id="number" required
                                    value="{{ old('number') }}">
                                @error('number')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button class="btn btn-outline-primary" type="submit">Search</button>
                        </form>
                        <br>
                        <h6 class="font-weight-bold">Add</h6>
                        <p>Want to record a new voucher? Click <a class="text-primary" href="{{ url('/vouchers/create') }}">here</a>!</p>
                        <br>
                        <h6 class="font-weight-bold">List</h6>
                        @forelse ($vouchers as $voucher)
                            <div id="content">
                                <div id="title">
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '{{ \App\Models\Voucher::find($voucher->id)->path(); }}'">View</button></div>
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '/vouchers/{{ $voucher->id }}/edit';">Edit</button></div>
                                    <div style="display:inline-block;"><form method="POST" action="/vouchers/{{ $voucher->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                                    </form></div><div style="display:inline-block;">&nbsp;&nbsp;Voucher ID {{ $voucher->number }}
                                        , {{ \App\Models\Bill::where('id', $voucher->bill_id)->firstOrFail()->payee->name }}
                                        , Bill no. {{ \App\Models\Bill::where('id', $voucher->bill_id)->firstOrFail()->bill_number }}
                                        , {{ \App\Models\Bill::where('id', $voucher->bill_id)->firstOrFail()->period_start }}
                                        , {{ \App\Models\Bill::where('id', $voucher->bill_id)->firstOrFail()->period_end }}
                                        , {{ \App\Models\Bill::where('id', $voucher->bill_id)->firstOrFail()->particulars }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>No vouchers recorded yet.</p>
                        @endforelse
                        {{ $vouchers->links() }}
                    @else
                        You are not authorized to browse vouchers.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
