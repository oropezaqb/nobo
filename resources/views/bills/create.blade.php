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
                        ->where('permissions.key', 'add_bills')->exists(); ?>
                    @if ($authorized)
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="POST" action="/bills">
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
                            <div class="form-group custom-control-inline">
                                <label for="received_at">Date received:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('received_at') is-danger @enderror" id="received_at" name="received_at" value="{!! old('received_at') !!}">
                            </div>
                            <br>
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
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="bill_number">Bill no. </label>
                                <input
                                    class="form-control"
                                    type="text"
                                    name="bill_number"
                                    id="bill_number"
                                    value="{{ old('bill_number') }}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="billed_at">Bill date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('billed_at') is-danger @enderror" id="billed_at" name="billed_at" value="{!! old('billed_at') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="po_number">P.O. no.</label>&nbsp;
                                <input type="text" class="form-control" id="po_number" name="po_number" style="text-align: right;"
                                    value="{!! old('po_number') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="due_at">Date due:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('due_at') is-danger @enderror" id="due_at" name="due_at" value="{!! old('due_at') !!}">
                            </div>
                            <br><br>
                            <div class="form-group custom-control-inline">
                                <label for="period_start">Start of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_start') is-danger @enderror" id="period_start" name="period_start" value="{!! old('period_start') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_end">End of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_end') is-danger @enderror" id="period_end" name="period_end" value="{!! old('period_end') !!}">
                            </div>
                            <div class="form-group">
                                <label for="particulars">Particulars </label>
                                <textarea class="form-control" rows="3" id="particulars" name="particulars">{{ old('particulars') }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="amount">Amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="amount" name="amount" step="0.01" style="text-align: right;"
                                    value="{!! old('amount') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="petty">PCF&nbsp;Replenishment/Reimbursement</label>&nbsp;
                                {{ Form::select('petty', array('0' => 'No', '1' => 'Yes'), '0', $attributes = array('class' => 'form-control', 'style' => 'width: 100px;',
                                    'value' => old('petty'), 'id' => 'petty', 'name' => 'petty')); }}
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="classification">Classification</label>&nbsp;
                                {{ Form::select('classification', array('OPEX' => 'OPEX', 'CAPEX' => 'CAPEX', 'Power' => 'Power'), 'OPEX',
                                    $attributes = array('class' => 'form-control', 'style' => 'width: 100px;',
                                    'value' => old('classificaion'), 'id' => 'classification', 'name' => 'classification')); }}
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="remarks">Remarks </label>
                                <textarea class="form-control" rows="3" id="remarks" name="remarks">{{ old('remarks') }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="endorsed_at">Date endorsed:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('endorsed_at') is-danger @enderror" id="endorsed_at" name="endorsed_at" value="{!! old('endorsed_at') !!}">
                            </div>
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                            <br>
                            <button class="btn btn-outline-primary" type="submit">Save</button>
                        </form>
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
                        You are not authorized to add bills.
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
