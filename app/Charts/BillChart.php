<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Voucher;

class BillChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
//    public ?string $name = 'bill_chart';
//    public ?string $routeName = 'bill_chart';
    public function handler(Request $request): Chartisan
    {
        $numberOfVouchers = Voucher::all()->count();
        $numberOfBills = Bill::all()->count();
        $numberOfBillsForProcessing = $numberOfBills - $numberOfVouchers;
        return Chartisan::build()
            ->labels(['Bills processed', 'Bills for processing'])
            ->dataset('Sample', [$numberOfVouchers, $numberOfBillsForProcessing]);
    }
}
