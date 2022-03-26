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
                        ->where('permissions.key', 'add_vouchers')->exists(); ?>
                    @if ($authorized)
                        <form method="POST" action="/vouchers">
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
                                <label for="number">Voucher no.</label>&nbsp;
                                <input type="number" class="form-control amount" id="number" name="number" step="1" style="text-align: right;"
                                    value="{!! old('number') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="bill_id">Find&nbsp;by&nbsp;bill&nbsp;id&nbsp;</label>&nbsp;
                                <input type="number" class="form-control" id="bill_id" name="bill_id" style="text-align: right;" required value="{!! old('bill_id') !!}" oninput="getBill()">
                            </div>
                            <div class="form-group">
                                <label for="payee_id">Payee:&nbsp;</label>&nbsp;
                                <input list="payee_ids" id="payee_id0" onchange="setValue(this)" data-id="" class="custom-select" value="{!! old('payee_id0') !!}" disabled>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="bill_number">Bill no. </label>
                                <input 
                                    class="form-control" 
                                    type="text" 
                                    name="bill_number" 
                                    id="bill_number"
                                    value="{{ old('bill_number') }}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_start">Start of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_start') is-danger @enderror" id="period_start" name="period_start" value="{!! old('period_start') !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_end">End of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_end') is-danger @enderror" id="period_end" name="period_end" value="{!! old('period_end') !!}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="particulars">Particulars </label>
                                <textarea class="form-control" rows="5" id="particulars" name="particulars" disabled>{{ old('particulars') }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="amount">Amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="amount" name="amount" step="0.01" style="text-align: right;"
                                    value="{!! old('amount') !!}" disabled>
                            </div>
                            <br><br>
                            <div class="form-group custom-control-inline">
                                <label for="date">Document date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('date') is-danger @enderror" id="date" name="date" value="{!! old('date') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="posted_at">Posting date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('posted_at') is-danger @enderror" id="posted_at" name="posted_at" value="{!! old('posted_at') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="payable_amount">Payable amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="payable_amount" name="payable_amount" step="0.01" style="text-align: right;"
                                    value="{!! old('payable_amount') !!}">
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks </label>
                                <textarea class="form-control" rows="5" id="remarks" name="remarks">{{ old('remarks') }}</textarea>
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
                            var bill = new Array();
                            var payeename = '';
                            function getBill()
                            {
                              var bill_id = document.getElementById('bill_id').value;
                              let _token = $('meta[name="csrf-token"]').attr('content');
                              $.ajaxSetup({
                                headers: {
                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                              });
                              $.ajax({
                                type:'POST',
                                url:'/vouchers/getbill',
                                data: {_token: _token, bill_id: bill_id},
                                dataType: 'json',
                                success:function(data) {
                                  bill = data.bill;
                                  payeename = data.payeename;
                                  billnumber = data.billnumber;
                                  periodstart = data.periodstart;
                                  periodend = data.periodend;
                                  particulars = data.particulars;
                                  amount = data.amount;
                                  if (bill === null) {
                                      document.getElementById('payee_id0').value = '';
                                      document.getElementById('bill_number').value = '';
                                      document.getElementById('period_start').value = '';
                                      document.getElementById('period_end').value = '';
                                      document.getElementById('particulars').value = '';
                                      document.getElementById('amount').value = '';
                                  }
                                  else {
                                      displayBill();
                                  }
                                },
                                error: function(data){
                                }
                              });
                            }
                            function displayBill()
                            {
                                document.getElementById('payee_id0').value = payeename;
                                document.getElementById('bill_number').value = billnumber;
                                document.getElementById('period_start').value = periodstart;
                                document.getElementById('period_end').value = periodend;
                                document.getElementById('particulars').value = particulars;
                                document.getElementById('amount').value = amount;
                            }
                        </script>
                    @else
                        You are not authorized to add vouchers.
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
