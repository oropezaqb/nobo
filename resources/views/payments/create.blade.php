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
                        ->where('permissions.key', 'add_payments')->exists(); ?>
                    @if ($authorized)
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if(!empty($messages))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($messages as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="/payments">
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
                                <label for="voucher_number">Find&nbsp;by&nbsp;voucher&nbsp;no.&nbsp;</label>&nbsp;
                                <input type="number" id="voucher_number" name="voucher_number" style="text-align: right;" data-id="" class="form-control"
                                    value="{!! old('voucher_number') !!}" oninput="getVoucher()">
                            </div>
                            <div class="form-group">
                                <label for="payee_id">Payee:&nbsp;</label>&nbsp;
                                <input list="payee_ids" id="payee_id0" onchange="setValue(this)" data-id="" class="custom-select"
                                    value="{!! old('payee_id0') !!}" disabled>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="bill_number">Bill no. </label>
                                <input class="form-control" type="text" name="bill_number" id="bill_number"
                                    value="{{ old('bill_number') }}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_start">Start of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_start') is-danger @enderror" id="period_start" name="period_start"
                                    value="{!! old('period_start') !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_end">End of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_end') is-danger @enderror" id="period_end" name="period_end"
                                    value="{!! old('period_end') !!}" disabled>
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
                                <input type="date" class="form-control @error('date') is-danger @enderror" id="date" name="date" value="{!! old('date') !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="posted_at">Posting date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('posted_at') is-danger @enderror" id="posted_at" name="posted_at" value="{!! old('posted_at') !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="payable_amount">Payable amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="payable_amount" name="payable_amount" step="0.01" style="text-align: right;"
                                    value="{!! old('payable_amount') !!}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks </label>
                                <textarea class="form-control" rows="5" id="remarks" name="remarks">{{ old('remarks') }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="check_number">Check&nbsp;no.</label>&nbsp;
                                <input type="text" class="form-control" id="check_number" name="check_number" style="text-align: right;"
                                    value="{!! old('check_number') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="check_date">Check&nbsp;date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('check_date') is-danger @enderror" id="check_date" name="check_date" value="{!! old('check_date') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="cancelled_checks">Cancelled&nbsp;checks</label>&nbsp;
                                <input type="text" class="form-control" id="cancelled_checks" name="cancelled_checks" style="text-align: left;"
                                    value="{!! old('cancelled_checks') !!}">
                            </div>
                            <br><br>
                            <div class="form-group custom-control-inline">
                                <label for="paid_at">Date&nbsp;paid:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('paid_at') is-danger @enderror" id="paid_at" name="paid_at" value="{!! old('paid_at') !!}">
                            </div>
                            <br><br>
                            <div class="form-group custom-control-inline">
                                <label for="cleared_at">Date&nbsp;cleared:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('cleared_at') is-danger @enderror" id="cleared_at" name="cleared_at" value="{!! old('cleared_at') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="cleared_amount">Cleared&nbsp;amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="cleared_amount" name="cleared_amount" step="0.01" style="text-align: right;"
                                    value="{!! old('cleared_amount') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="service_charge">Service&nbsp;charge</label>&nbsp;
                                <input type="number" class="form-control amount" id="service_charge" name="service_charge" step="0.01" style="text-align: right;"
                                    value="{!! old('service_charge') !!}">
                            </div>
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" id="voucher_id" name="voucher_id" value="{!! old('voucher_id') !!}">
                            <br><br>
                            <div class="form-group custom-control-inline">
                                <label for="receipt_number">Receipt&nbsp;no.</label>&nbsp;
                                <input type="text" class="form-control" id="receipt_number" name="receipt_number" style="text-align: right;"
                                    value="{!! old('receipt_number') !!}">
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="receipt_received_at">Receipt&nbsp;received&nbsp;at</label>&nbsp;
                                <input type="date" class="form-control @error('receipt_received_at') is-danger @enderror" id="receipt_received_at" name="receipt_received_at" value="{!! old('receipt_received_at') !!}">
                            </div>
                            <br>
                            <button class="btn btn-outline-primary" type="submit">Save</button>
                        </form>
                        <br><br>
                        <form method="POST" action="/payments/upload" enctype="multipart/form-data">
                            @csrf
                            <h6 class="font-weight-bold">Import</h6>
                            <div class="form-group">
                                <label for="payments">Select a CSV file to upload (Voucher No., Check No., Check Date, Date Paid, Date Cleared)</label>
                                <br>
                                {!! Form::file('payments') !!}
                                @error('payments')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button class="btn btn-outline-primary" type="submit">Import</button>
                        </form>
                        <script>
                            function setValue(id)
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
                                getBill();
                            }
                            var voucher = new Array();
                            var payeename = '';
                            function getVoucher()
                            {
                              var voucher_number = document.getElementById('voucher_number').value;
                              let _token = $('meta[name="csrf-token"]').attr('content');
                              $.ajaxSetup({
                                headers: {
                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                              });
                              $.ajax({
                                type:'POST',
                                url:'/reviewed-vouchers/get-voucher',
                                data: {_token: _token, voucher_number: voucher_number},
                                dataType: 'json',
                                success:function(data) {
                                  voucher = data.voucher;
                                  payeename = data.payeename;
                                  billnumber = data.billnumber;
                                  periodstart = data.periodstart;
                                  periodend = data.periodend;
                                  particulars = data.particulars;
                                  amount = data.amount;
                                  voucherdate = data.date;
                                  postedat = data.postedat;
                                  payableamount = data.payableamount;
                                  if (voucher === null) {
                                      document.getElementById('payee_id0').value = '';
                                      document.getElementById('bill_number').value = '';
                                      document.getElementById('period_start').value = '';
                                      document.getElementById('period_end').value = '';
                                      document.getElementById('particulars').value = '';
                                      document.getElementById('amount').value = '';
                                      document.getElementById('date').value = '';
                                      document.getElementById('posted_at').value = '';
                                      document.getElementById('payable_amount').value = '';
                                  }
                                  else {
                                      displayVoucher();
                                  }
                                },
                                error: function(data){
                                }
                              });
                            }
                            function displayVoucher()
                            {
                                document.getElementById('voucher_id').value = voucher['id'];
                                document.getElementById('payee_id0').value = payeename;
                                document.getElementById('bill_number').value = billnumber;
                                document.getElementById('period_start').value = periodstart;
                                document.getElementById('period_end').value = periodend;
                                document.getElementById('particulars').value = particulars;
                                document.getElementById('amount').value = amount;
                                document.getElementById('date').value = voucherdate;
                                document.getElementById('posted_at').value = postedat;
                                document.getElementById('payable_amount').value = payableamount;
                            }
                        </script>
                    @else
                        You are not authorized to add payments.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
