<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
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

                    <div class="row">
                        <div class="col-sm-4" id="bill_chart" style="height: 300px;"></div>
                        <div class="col-sm-4" id="voucher_chart" style="height: 300px;"></div>
                        <div class="col-sm-4" id="reviewed_voucher_chart" style="height: 300px;"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4" style="display:flex;justify-content:center;">
                            <div style="display:inline-block;"><form method="POST" action="/reports/bills-for-processing">
                                @csrf
                                <button class="btn btn-link" type="submit">Run</button>
                            </form></div>
                            <div style="display:inline-block;"><form method="POST" action="/reports/bills-for-processing-csv">
                                @csrf
                                <button class="btn btn-link" type="submit">CSV</button>
                            </form></div>
                        </div>
                        <div class="col-sm-4" style="display:flex;justify-content:center;">
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-review">
                                @csrf
                                <button class="btn btn-link" type="submit">Run</button>
                            </form></div>
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-review-csv">
                                @csrf
                                <button class="btn btn-link" type="submit">CSV</button>
                            </form></div>
                        </div>
                        <div class="col-sm-4" style="display:flex;justify-content:center;">
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-approval">
                                @csrf
                                <button class="btn btn-link" type="submit">Run</button>
                            </form></div>
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-approval-csv">
                                @csrf
                                <button class="btn btn-link" type="submit">CSV</button>
                            </form></div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4" id="approved_voucher_chart" style="height: 300px;"></div>
                        <div class="col-sm-4" id="bank_endorsement_chart" style="height: 300px;"></div>
                        <div class="col-sm-4" id="payment_chart" style="height: 300px;"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4" style="display:flex;justify-content:center;">
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-HO-endorsement">
                                @csrf
                                <button class="btn btn-link" type="submit">Run</button>
                            </form></div>
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-HO-endorsement-csv">
                                @csrf
                                <button class="btn btn-link" type="submit">CSV</button>
                            </form></div>
                        </div>
                        <div class="col-sm-4" style="display:flex;justify-content:center;">
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-bank-endorsement">
                                @csrf
                                <button class="btn btn-link" type="submit">Run</button>
                            </form></div>
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-bank-endorsement-csv">
                                @csrf
                                <button class="btn btn-link" type="submit">CSV</button>
                            </form></div>
                        </div>
                        <div class="col-sm-4" style="display:flex;justify-content:center;">
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-payment">
                                @csrf
                                <button class="btn btn-link" type="submit">Run</button>
                            </form></div>
                            <div style="display:inline-block;"><form method="POST" action="/reports/vouchers-for-payment-csv">
                                @csrf
                                <button class="btn btn-link" type="submit">CSV</button>
                            </form></div>
                        </div>
                    </div>
                    <br><br>
                    <h6 class="font-weight-bold">AP Aging</h6>
                    <br>
                    @php
                        $dateToday = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $dateToday = $dateToday->format('Y-m-d');
                        $currentDate = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $currentDate->add(new DateInterval('P30D'));
                        $currentDate = $currentDate->format('Y-m-d');
                        $oneToThirtyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $oneToThirtyDays->sub(new DateInterval('P30D'));
                        $oneToThirtyDays = $oneToThirtyDays->format('Y-m-d');
                        $thirtyOneToSixtyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $thirtyOneToSixtyDays->sub(new DateInterval('P60D'));
                        $thirtyOneToSixtyDays = $thirtyOneToSixtyDays->format('Y-m-d');
                        $sixtyOneToNinetyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $sixtyOneToNinetyDays->sub(new DateInterval('P90D'));
                        $sixtyOneToNinetyDays = $sixtyOneToNinetyDays->format('Y-m-d');
                    @endphp
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th style="text-align:center;">Count</th>
                                        <th style="text-align:right;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/current-accounts-payable">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/current-accounts-payable-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;Current</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $dateToday)
                                            ->where('due_at', '<=', $currentDate)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $dateToday)
                                            ->where('due_at', '<=', $currentDate)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/accounts-payable-thirty">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/accounts-payable-thirty-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;1-30 days past due</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $oneToThirtyDays)
                                            ->where('due_at', '<', $dateToday)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $oneToThirtyDays)
                                            ->where('due_at', '<', $dateToday)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/accounts-payable-sixty">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/accounts-payable-sixty-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;31-60 days past due</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $thirtyOneToSixtyDays)
                                            ->where('due_at', '<', $oneToThirtyDays)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $thirtyOneToSixtyDays)
                                            ->where('due_at', '<', $oneToThirtyDays)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/accounts-payable-ninety">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/accounts-payable-ninety-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;61-90 days past due</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $sixtyOneToNinetyDays)
                                            ->where('due_at', '<', $thirtyOneToSixtyDays)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $sixtyOneToNinetyDays)
                                            ->where('due_at', '<', $thirtyOneToSixtyDays)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/accounts-payable-ninetyplus">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/accounts-payable-ninetyplus-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;90+ days past due</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '<', $sixtyOneToNinetyDays)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '<', $sixtyOneToNinetyDays)
                                            ->where('petty', '0')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br><br>
                    <h6 class="font-weight-bold">PCF Replenishment/Reimbursement Aging</h6>
                    <br>
                    @php
                        $dateToday = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $dateToday = $dateToday->format('Y-m-d');
                        $currentDate = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $currentDate->add(new DateInterval('P30D'));
                        $currentDate = $currentDate->format('Y-m-d');
                        $oneToThirtyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $oneToThirtyDays->sub(new DateInterval('P7D'));
                        $oneToThirtyDays = $oneToThirtyDays->format('Y-m-d');
                        $thirtyOneToSixtyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $thirtyOneToSixtyDays->sub(new DateInterval('P14D'));
                        $thirtyOneToSixtyDays = $thirtyOneToSixtyDays->format('Y-m-d');
                        $sixtyOneToNinetyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $sixtyOneToNinetyDays->sub(new DateInterval('P21D'));
                        $sixtyOneToNinetyDays = $sixtyOneToNinetyDays->format('Y-m-d');
                    @endphp
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th style="text-align:center;">Count</th>
                                        <th style="text-align:right;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/petty-current">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/petty-current-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;Current</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $dateToday)
                                            ->where('due_at', '<=', $currentDate)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $dateToday)
                                            ->where('due_at', '<=', $currentDate)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/petty-seven">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/petty-seven-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;1-7 days past due</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $oneToThirtyDays)
                                            ->where('due_at', '<', $dateToday)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $oneToThirtyDays)
                                            ->where('due_at', '<', $dateToday)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/petty-fourteen">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/petty-fourteen-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;8-14 days past due</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $thirtyOneToSixtyDays)
                                            ->where('due_at', '<', $oneToThirtyDays)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $thirtyOneToSixtyDays)
                                            ->where('due_at', '<', $oneToThirtyDays)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/petty-twenty-one">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/petty-twenty-one-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;15-21 days past due</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $sixtyOneToNinetyDays)
                                            ->where('due_at', '<', $thirtyOneToSixtyDays)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '>=', $sixtyOneToNinetyDays)
                                            ->where('due_at', '<', $thirtyOneToSixtyDays)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><div style="display:inline-block;"><form method="POST" action="/reports/petty-twenty-one-plus">
                                            @csrf
                                            <button class="btn btn-link" type="submit">Run</button>
                                            </form></div>
                                            <div style="display:inline-block;"><form method="POST" action="/reports/petty-twenty-one-plus-csv">
                                            @csrf
                                            <button class="btn btn-link" type="submit">CSV</button>
                                            </form></div>&nbsp;&nbsp;21+ days past due</td>
                                        <td style="text-align:center;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '<', $sixtyOneToNinetyDays)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->count()) }}</td>
                                        <td style="text-align:right;vertical-align:middle;">{{ number_format(\DB::table('bills')
                                            ->leftJoin('vouchers', 'bills.id', '=', 'vouchers.bill_id')
                                            ->leftJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                                            ->where('due_at', '<', $sixtyOneToNinetyDays)
                                            ->where('petty', '1')
                                            ->whereNull('payments.paid_at')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                        Contact admin for authorization.
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script>
    <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
    <script src="https://unpkg.com/chart.js@^2.9.3/dist/Chart.min.js"></script>
    <script src="https://unpkg.com/@chartisan/chartjs@^2.1.0/dist/chartisan_chartjs.umd.js"></script>
    <script>
      const bill_chart = new Chartisan({
        el: '#bill_chart',
        url: "@chart('bill_chart')",
        hooks: new ChartisanHooks()
          .datasets('doughnut')
          .pieColors(),
      });
      const voucher_chart = new Chartisan({
        el: '#voucher_chart',
        url: "@chart('voucher_chart')",
        hooks: new ChartisanHooks()
          .datasets('doughnut')
          .pieColors(),
      });
      const reviewed_voucher_chart = new Chartisan({
        el: '#reviewed_voucher_chart',
        url: "@chart('reviewed_voucher_chart')",
        hooks: new ChartisanHooks()
          .beginAtZero()
          .colors(),
      });
      const approved_voucher_chart = new Chartisan({
        el: '#approved_voucher_chart',
        url: "@chart('approved_voucher_chart')",
        hooks: new ChartisanHooks()
          .datasets('doughnut')
          .pieColors(),
      });
      const bank_endorsement_chart = new Chartisan({
        el: '#bank_endorsement_chart',
        url: "@chart('bank_endorsement_chart')",
        hooks: new ChartisanHooks()
          .beginAtZero()
          .colors(),
      });
      const payment_chart = new Chartisan({
        el: '#payment_chart',
        url: "@chart('payment_chart')",
        hooks: new ChartisanHooks()
          .beginAtZero()
          .colors(),
      });
    </script>
</x-app-layout>
