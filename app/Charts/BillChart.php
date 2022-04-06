<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Voucher;
use DateTime;
use DateTimeZone;
use DateInterval;

class BillChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $dueDate = new DateTime("now", new DateTimeZone('Asia/Manila'));
        $dueDate->add(new DateInterval('P14D'));
        $dueDate = $dueDate->format('Y-m-d');
        $numberOfVouchers = \DB::table('vouchers')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->count();
        $numberOfBills = Bill::where('due_at', '<=', $dueDate)->count();
        $numberOfBillsForProcessing = $numberOfBills - $numberOfVouchers;
        return Chartisan::build()
            ->labels(['Bills processed', 'Bills for processing'])
            ->dataset('Sample', [$numberOfVouchers, $numberOfBillsForProcessing]);
    }
}
