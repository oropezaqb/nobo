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
                    <div class="row">
                        <div class="col-sm-4" id="bill_chart" style="height: 300px;"></div>
                        <div class="col-sm-4" id="voucher_chart" style="height: 300px;"></div>
                        <div class="col-sm-4" id="reviewed_voucher_chart" style="height: 300px;"></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4" id="approved_voucher_chart" style="height: 300px;"></div>
                        <div class="col-sm-4" id="bank_endorsement_chart" style="height: 300px;"></div>
                        <div class="col-sm-4" id="payment_chart" style="height: 300px;"></div>
                    </div>
                    <br><br>
                    <h6 class="font-weight-bold">AP Aging</h6>
                    <br>
                    @php
                        $dateToday = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $currentDate = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $currentDate->add(new DateInterval('P30D'));
                        $oneToThirtyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $oneToThirtyDays->sub(new DateInterval('P30D'));
                        $thirtyOneToSixtyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $thirtyOneToSixtyDays->sub(new DateInterval('P60D'));
                        $sixtyOneToNinetyDays = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $sixtyOneToNinetyDays->sub(new DateInterval('P90D'));
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
                                        <td>Current</td>
                                        <td style="text-align:center;">{{ number_format(\DB::table('bills')
                                            ->whereBetween('due_at', [$dateToday, $currentDate])
                                            ->where('petty', '0')
                                            ->count()) }}</td>
                                        <td style="text-align:right;">{{ number_format(\DB::table('bills')
                                            ->whereBetween('due_at', [$dateToday, $currentDate])
                                            ->where('petty', '0')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>1-30 days past due</td>
                                        <td style="text-align:center;">{{ number_format(\DB::table('bills')
                                            ->whereBetween('due_at', [$oneToThirtyDays, $dateToday])
                                            ->where('petty', '0')
                                            ->count()) }}</td>
                                        <td style="text-align:right;">{{ number_format(\DB::table('bills')
                                            ->whereBetween('due_at', [$oneToThirtyDays, $dateToday])
                                            ->where('petty', '0')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>31-60 days past due</td>
                                        <td style="text-align:center;">{{ number_format(\DB::table('bills')
                                            ->whereBetween('due_at', [$thirtyOneToSixtyDays, $oneToThirtyDays])
                                            ->where('petty', '0')
                                            ->count()) }}</td>
                                        <td style="text-align:right;">{{ number_format(\DB::table('bills')
                                            ->whereBetween('due_at', [$thirtyOneToSixtyDays, $oneToThirtyDays])
                                            ->where('petty', '0')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>61-90 days past due</td>
                                        <td style="text-align:center;">{{ number_format(\DB::table('bills')
                                            ->whereBetween('due_at', [$sixtyOneToNinetyDays, $thirtyOneToSixtyDays])
                                            ->where('petty', '0')
                                            ->count()) }}</td>
                                        <td style="text-align:right;">{{ number_format(\DB::table('bills')
                                            ->whereBetween('due_at', [$sixtyOneToNinetyDays, $thirtyOneToSixtyDays])
                                            ->where('petty', '0')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>90+ days past due</td>
                                        <td style="text-align:center;">{{ number_format(\DB::table('bills')
                                            ->where('due_at', '<', $sixtyOneToNinetyDays)
                                            ->where('petty', '0')
                                            ->count()) }}</td>
                                        <td style="text-align:right;">{{ number_format(\DB::table('bills')
                                            ->where('due_at', '<', $sixtyOneToNinetyDays)
                                            ->where('petty', '0')
                                            ->sum('amount'), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
