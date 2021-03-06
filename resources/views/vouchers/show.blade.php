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
                        ->where('permissions.key', 'read_vouchers')->exists(); ?>
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
                                    value="{!! old('number', $voucher->number) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="bill_id">Find&nbsp;by&nbsp;bill&nbsp;id&nbsp;</label>&nbsp;
                                <input type="text" list="bill_ids" id="bill_id" name="bill_id" style="text-align: right;" data-id="" class="form-control"
                                    value="{!! old('bill_id', $voucher->bill_id) !!}" oninput="getBill()" disabled>
                            </div>
                            <div class="form-group">
                                <label for="payee_id">Payee:&nbsp;</label>&nbsp;
                                <input list="payee_ids" id="payee_id0" onchange="setValue(this)" data-id="" class="custom-select"
                                    value="{!! old('payee_id0', $voucher->bill->payee->name) !!}" disabled>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="bill_number">Bill no. </label>
                                <input class="form-control" type="text" name="bill_number" id="bill_number"
                                    value="{{ old('bill_number', $voucher->bill->bill_number) }}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_start">Start of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_start') is-danger @enderror" id="period_start" name="period_start"
                                    value="{!! old('period_start', $voucher->bill->period_start) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_end">End of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_end') is-danger @enderror" id="period_end" name="period_end"
                                    value="{!! old('period_end', $voucher->bill->period_end) !!}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="particulars">Particulars </label>
                                <textarea class="form-control" rows="5" id="particulars" name="particulars" disabled>{{ old('particulars', $voucher->bill->particulars) }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="amount">Amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="amount" name="amount" step="0.01" style="text-align: right;"
                                    value="{!! old('amount', $voucher->bill->amount) !!}" disabled>
                            </div>
                            <br><br>
                            <div class="form-group custom-control-inline">
                                <label for="date">Document date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('date') is-danger @enderror" id="date" name="date" value="{!! old('date', $voucher->date) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="posted_at">Posting date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('posted_at') is-danger @enderror" id="posted_at" name="posted_at" value="{!! old('posted_at', $voucher->posted_at) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="payable_amount">Payable amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="payable_amount" name="payable_amount" step="0.01" style="text-align: right;"
                                    value="{!! old('payable_amount', $voucher->payable_amount) !!}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks </label>
                                <textarea class="form-control" rows="5" id="remarks" name="remarks" disabled>{{ old('remarks', $voucher->remarks) }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="endorsed_at">Endorsed for review&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('endorsed_at') is-danger @enderror" id="endorsed_at" name="endorsed_at" value="{!! old('endorsed_at', $voucher->endorsed_at) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label>User&nbsp;</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    value="{{ $voucher->user->name ?? '' }}" disabled>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="remarks">Review remarks </label>
                                <textarea class="form-control" rows="3" id="remarks" name="remarks" disabled>{{ old('remarks', $voucher->reviewedVoucher->remarks ?? '') }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="endorsed_at">Endorsed for approval&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('endorsed_at') is-danger @enderror" id="endorsed_at" name="endorsed_at"
                                    value="{!! old('endorsed_at', $voucher->reviewedVoucher->endorsed_at ?? '') !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label>User&nbsp;</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    value="{{ $voucher->reviewedVoucher->user->name ?? '' }}" disabled>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="remarks">Approval remarks </label>
                                <textarea class="form-control" rows="3" id="remarks" name="remarks" disabled>{{ old('remarks', $voucher->approvedVoucher->remarks ?? '') }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="endorsed_at">Endorsed to HO&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('endorsed_at') is-danger @enderror" id="endorsed_at" name="endorsed_at"
                                    value="{!! old('endorsed_at', $voucher->approvedVoucher->endorsed_at ?? '') !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label>User&nbsp;</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    value="{{ $voucher->approvedVoucher->user->name ?? '' }}" disabled>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="remarks">Bank endorsement remarks </label>
                                <textarea class="form-control" rows="3" id="remarks" name="remarks"
                                    disabled>{{ old('remarks', $voucher->bankEndorsement->remarks ?? '') }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="endorsed_at">Endorsed to bank&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('endorsed_at') is-danger @enderror" id="endorsed_at" name="endorsed_at"
                                    value="{!! old('endorsed_at', $voucher->bankEndorsement->endorsed_at ?? '') !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label>User&nbsp;</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    value="{{ $voucher->bankEndorsement->user->name ?? '' }}" disabled>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="remarks">Payment remarks </label>
                                <textarea class="form-control" rows="3" id="remarks" name="remarks"
                                    disabled>{{ old('remarks', $voucher->payment->remarks ?? '') }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="endorsed_at">Date paid&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('endorsed_at') is-danger @enderror" id="endorsed_at" name="endorsed_at"
                                    value="{!! old('endorsed_at', $voucher->payment->paid_at ?? '') !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label>User&nbsp;</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    value="{{ $voucher->payment->user->name ?? '' }}" disabled>
                            </div>
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                            <br>
                        </form>
                        <div style="clear: both;">
                            <div style="display: inline-block;">
                                <button class="btn btn-outline-primary" onclick="location.href = '/vouchers/{{ $voucher->id }}/edit';">Edit</button>
                            </div>
                            <div style="display: inline-block;">
                                <form method="POST" action="/vouchers/{{ $voucher->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                                </form>
                            </div>
                        </div>
                        <br><br>
                        <form method="POST" action="/vouchers/{{ $voucher->id }}/cancel">
                            @csrf
                            <h6 class="font-weight-bold">Cancel</h6>
                            <div class="form-group">
                                <label for="remarks">Reason for cancellation </label>
                                <textarea class="form-control" rows="3" id="reason_for_cancellation" name="reason_for_cancellation"
                                    >{{ old('reason_for_cancellation') }}</textarea>
                            </div>
                            <button class="btn btn-outline-warning" type="submit" onclick="return confirm('Are you sure you want to cancel this item?');">Cancel</button>
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
                        You are not authorized to view vouchers.
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
