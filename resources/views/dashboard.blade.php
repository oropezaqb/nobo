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
