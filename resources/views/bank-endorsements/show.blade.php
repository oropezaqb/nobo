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
                        ->where('permissions.key', 'read_bank_endorsements')->exists(); ?>
                    @if ($authorized)
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="POST" action="/bank-endorsements">
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
                                    value="{!! old('voucher_number', $bankEndorsement->voucher->number) !!}" oninput="getVoucher()" disabled>
                            </div>
                            <div class="form-group">
                                <label for="payee_id">Payee:&nbsp;</label>&nbsp;
                                <input list="payee_ids" id="payee_id0" onchange="setValue(this)" data-id="" class="custom-select"
                                    value="{!! old('payee_id0', $bankEndorsement->voucher->bill->payee->name) !!}" disabled>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="bill_number">Bill no. </label>
                                <input class="form-control" type="text" name="bill_number" id="bill_number"
                                    value="{{ old('bill_number', $bankEndorsement->voucher->bill->bill_number) }}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_start">Start of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_start') is-danger @enderror" id="period_start" name="period_start"
                                    value="{!! old('period_start', $bankEndorsement->voucher->bill->period_start) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="period_end">End of period:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('period_end') is-danger @enderror" id="period_end" name="period_end"
                                    value="{!! old('period_end', $bankEndorsement->voucher->bill->period_end) !!}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="particulars">Particulars </label>
                                <textarea class="form-control" rows="5" id="particulars" name="particulars" disabled>{{ old('particulars', $bankEndorsement->voucher->bill->particulars) }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="amount">Amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="amount" name="amount" step="0.01" style="text-align: right;"
                                    value="{!! old('amount', $bankEndorsement->voucher->bill->amount) !!}" disabled>
                            </div>
                            <br><br>
                            <div class="form-group custom-control-inline">
                                <label for="date">Document date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('date') is-danger @enderror" id="date" name="date" value="{!! old('date', $bankEndorsement->voucher->date) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="posted_at">Posting date:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('posted_at') is-danger @enderror" id="posted_at" name="posted_at" value="{!! old('posted_at', $bankEndorsement->voucher->posted_at) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="payable_amount">Payable amount</label>&nbsp;
                                <input type="number" class="form-control amount" id="payable_amount" name="payable_amount" step="0.01" style="text-align: right;"
                                    value="{!! old('payable_amount', $bankEndorsement->voucher->payable_amount) !!}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks </label>
                                <textarea class="form-control" rows="5" id="remarks" name="remarks" disabled>{{ old('remarks', $bankEndorsement->remarks) }}</textarea>
                            </div>
                            <br>
                            <div class="form-group custom-control-inline">
                                <label for="approved_at">Date approved:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('approved_at') is-danger @enderror" id="approved_at" name="approved_at" value="{!! old('approved_at', $bankEndorsement->approved_at) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label for="endorsed_at">Date endorsed:&nbsp;</label>&nbsp;
                                <input type="date" class="form-control @error('endorsed_at') is-danger @enderror" id="endorsed_at" name="endorsed_at" value="{!! old('endorsed_at', $bankEndorsement->endorsed_at) !!}" disabled>
                            </div>
                            <div class="form-group custom-control-inline">
                                <label>User&nbsp;</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    value="{{ $bankEndorsement->user->name ?? '' }}" disabled>
                            </div>
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" id="voucher_id" name="voucher_id" value="{!! old('voucher_id') !!}">
                            <br>
                        </form>
                        <div style="clear: both;">
                            <div style="display: inline-block;">
                                <button class="btn btn-outline-primary" onclick="location.href = '/bank-endorsements/{{ $bankEndorsement->id }}/edit';">Edit</button>
                            </div>
                            <div style="display: inline-block;">
                                <form method="POST" action="/bank-endorsements/{{ $bankEndorsement->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
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
                        You are not authorized to view bank endorsements.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
