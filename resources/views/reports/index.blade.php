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
                        <h6 class="font-weight-bold">Bills for Payment (Current and Past Due)</h6>
                        <form method="POST" action="/reports/bills-for-payment">
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
                                <label for="payee_id">Payee:&nbsp;</label>&nbsp;
                                <input list="payee_ids" id="payee_id0" onchange="setValue(this)" data-id="" class="custom-select" value="{!! old('payee_name') !!}">
                                <datalist id="payee_ids">
                                    @foreach ($payees as $payee)
                                        <option data-value="{{ $payee->id }}">{{ $payee->name }}</option>
                                    @endforeach
                                </datalist>
                                <input type="hidden" name="payee_id" id="payee_id0-hidden" value="{!! old('payee_id') !!}">
                                <input type="hidden" name="payee_name" id="name-payee_id0-hidden" value="{!! old('payee_name') !!}">
                            </div>
                        </form>
                        <div style="display:inline-block;">
                            <form method="POST" action="/reports/bills-for-payment">
                                @csrf
                                <input type="hidden" name="payee_id" id="payee_id1-hidden" value="{!! old('payee_id') !!}">
                                <button class="btn btn-outline-primary" type="submit">Run</button>
                            </form>
                        </div>
                        <div style="display:inline-block;">
                            <form method="POST" action="/reports/bills-for-payment-csv">
                                @csrf
                                <input type="hidden" name="payee_id" id="payee_id2-hidden" value="{!! old('payee_id') !!}">
                                <button class="btn btn-outline-primary" type="submit">CSV</button>
                            </form>
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
                                        document.getElementById('payee_id1-hidden').value = option.getAttribute('data-value');
                                        document.getElementById('payee_id2-hidden').value = option.getAttribute('data-value');
                                        break;
                                    }
                                }
                            }
                        </script>
                    @else
                        You are not authorized to browse queries and reports.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
