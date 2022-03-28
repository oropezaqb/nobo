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
                        ->where('permissions.key', 'browse_bills')->exists(); ?>
                    @if ($browse)
                        <h6 class="font-weight-bold">Search</h6>
                        <form method="GET" action="/bills">
                            @csrf
                            <div class="form-group custom-control-inline">
                                <label for="payee">Payee </label>
                                <input
                                    class="form-control @error('payee') is-danger @enderror"
                                    type="text"
                                    name="payee"
                                    id="payee" required
                                    value="{{ old('payee') }}">
                                @error('bill_payee')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="bill_number">Bill no.  </label>
                                <input
                                    class="form-control @error('bill_number') is-danger @enderror"
                                    type="text"
                                    name="bill_number"
                                    id="bill_number"
                                    value="{{ old('bill_number') }}">
                                @error('bill_number')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button class="btn btn-outline-primary" type="submit">Search</button>
                        </form>
                        <h6 class="font-weight-bold">Add</h6>
                        <p>Want to record a new bill? Click <a class="text-primary" href="{{ url('/bills/create') }}">here</a>!</p>
                        <br>
                        <h6 class="font-weight-bold">List</h6>
                        @forelse ($bills as $bill)
                            <div id="content">
                                <div id="title">
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '{{ \App\Models\Bill::find($bill->id)->path(); }}'">View</button></div>
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '/bills/{{ $bill->id }}/edit';">Edit</button></div>
                                    <div style="display:inline-block;"><form method="POST" action="/bills/{{ $bill->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit">Delete</button>
                                    </form></div>
                                    <div style="display:inline-block;">&nbsp;&nbsp;Bill ID {{ $bill->id }}
                                        , {{ \App\Models\Bill::where('id', $bill->id)->firstOrFail()->payee->name }}
                                        , Bill no. {{ $bill->bill_number }}
                                        , {{ $bill->period_start }}
                                        , {{ $bill->period_end }}
                                        , {{ $bill->particulars }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>No bills recorded yet.</p>
                        @endforelse
                        {{ $bills->links() }}
                    @else
                        You are not authorized to browse bills.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
