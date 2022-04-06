<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\ApprovedVoucher;
use DateTime;
use DateTimeZone;
use DateInterval;

class ApprovedVoucherChart extends BaseChart
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
        $numberOfApprovedVouchers = \DB::table('approved_vouchers')
            ->leftJoin('vouchers', 'approved_vouchers.voucher_id', '=', 'vouchers.id')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->count();
        $numberOfEndorsedToHO = \DB::table('approved_vouchers')
            ->leftJoin('vouchers', 'approved_vouchers.voucher_id', '=', 'vouchers.id')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->whereNotNull('approved_vouchers.endorsed_at')
            ->count();
        $numberOfForEndorsement = $numberOfApprovedVouchers - $numberOfEndorsedToHO;
        return Chartisan::build()
            ->labels(['Endorsed to HO', 'For endorsement'])
            ->dataset('Sample', [$numberOfEndorsedToHO, $numberOfForEndorsement]);
    }
}
