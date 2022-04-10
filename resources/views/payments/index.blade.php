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
                        ->where('permissions.key', 'browse_payments')->exists(); ?>
                    @if ($browse)
                        <h6 class="font-weight-bold">Search</h6>
                        <form method="GET" action="/payments">
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
                        <p>Want to record a new payment? Click <a class="text-primary" href="{{ url('/payments/create') }}">here</a>!</p>
                        <br>
                        <h6 class="font-weight-bold">List</h6>
                        @forelse ($payments as $payment)
                            <div id="content">
                                <div id="title">
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '{{ \App\Models\Payment::find($payment->id)->path(); }}'">View</button></div>
                                    <div style="display:inline-block;"><button class="btn btn-link" onclick="location.href = '/payments/{{ $payment->id }}/edit';">Edit</button></div>
                                    <div style="display:inline-block;"><form method="POST" action="/payments/{{ $payment->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit">Delete</button>
                                    </form></div><div style="display:inline-block;">&nbsp;&nbsp;Voucher ID {{ \App\Models\Payment::find($payment->id)->voucher->number }}
                                        , {{ \App\Models\Payment::find($payment->id)->voucher->bill->payee->name }}
                                        , Bill no. {{ \App\Models\Payment::find($payment->id)->voucher->bill->bill_number }}
                                        , {{ \App\Models\Payment::find($payment->id)->voucher->bill->period_start }}
                                        , {{ \App\Models\Payment::find($payment->id)->voucher->bill->period_end }}
                                        , {{ \App\Models\Payment::find($payment->id)->voucher->bill->particulars }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>No payments recorded yet.</p>
                        @endforelse
                        {{ $payments->links() }}
                    @else
                        You are not authorized to browse payments.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
