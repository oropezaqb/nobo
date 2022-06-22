<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\ReviewedVoucher;
use App\Models\ApprovedVoucher;
use DateTime;
use DateTimeZone;
use DateInterval;

class ReviewedVoucherChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $dueDate = new DateTime("now", new DateTimeZone('Asia/Manila'));
        $dueDate->add(new DateInterval('P30D'));
        $dueDate = $dueDate->format('Y-m-d');
        $numberOfReviewedVouchers = \DB::table('reviewed_vouchers')
            ->leftJoin('vouchers', 'reviewed_vouchers.voucher_id', '=', 'vouchers.id')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->count();
        $numberOfApprovedVouchers = \DB::table('approved_vouchers')
            ->leftJoin('vouchers', 'approved_vouchers.voucher_id', '=', 'vouchers.id')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->count();
        return Chartisan::build()
            ->labels(['Vouchers'])
            ->dataset('Approved', [$numberOfApprovedVouchers])
            ->dataset('Endorsed for approval', [$numberOfReviewedVouchers]);
    }
}
