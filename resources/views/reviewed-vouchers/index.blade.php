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
                        ->where('permissions.key', 'browse_reviewed_vouchers')->exists(); ?>
                    @if ($browse)
                        <h6 class="font-weight-bold">Search</h6>
                        <form method="GET" action="/reviewed-vouchers">
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
                        <p>Want to record a new reviewed voucher? Click <a class="text-primary" href="{{ url('/reviewed-vouchers/create') }}">here</a>!</p>
                        <br>
                        <h6 class="font-weight-bold">List</h6>
                        @forelse ($reviewedVouchers as $reviewedVoucher)
                            <div id="content">
                                <div id="title">
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '{{ \App\Models\ReviewedVoucher::find($revieweVouchers->id)->path(); }}'">View</button></div>
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '/reviewed-vouchers/{{ $revieweVouchers->id }}/edit';">Edit</button></div>
                                    <div style="display:inline-block;"><form method="POST" action="/reviewed-vouchers/{{ $revieweVouchers->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit">Delete</button>
                                    </form></div><div style="display:inline-block;">&nbsp;&nbsp;Voucher ID {{ $reviewedVoucher->voucher->number }}
                                        , {{ \App\Models\Bill::where('id', $reviewedVoucher->voucher->bill_id)->firstOrFail()->payee->name }}
                                        , Bill no. {{ \App\Models\Bill::where('id', $voucher->bill_id)->firstOrFail()->bill_number }}
                                        , {{ \App\Models\Bill::where('id', $reviewedVoucher->voucher->bill_id)->firstOrFail()->period_start }}
                                        , {{ \App\Models\Bill::where('id', $reviewedVoucher->voucher->bill_id)->firstOrFail()->period_end }}
                                        , {{ \App\Models\Bill::where('id', $reviewedVoucher->voucher->bill_id)->firstOrFail()->particulars }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>No reviewed vouchers recorded yet.</p>
                        @endforelse
                    @else
                        You are not authorized to browse reviewed vouchers.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
