<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\ReviewedVoucher;
use App\Models\ApprovedVoucher;

class ReviewedVoucherChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $numberOfReviewedVouchers = ReviewedVoucher::all()->count();
        $numberOfApprovedVouchers = ApprovedVoucher::all()->count();
        return Chartisan::build()
//            ->labels(['Vouchers revieved', 'Vouchers approved'])
//            ->dataset('Sample', [$numberOfReviewedVouchers, $numberOfApprovedVouchers]);
            ->labels(['Vouchers'])
            ->dataset('Approved', [$numberOfApprovedVouchers])
            ->dataset('For approval', [$numberOfReviewedVouchers]);
    }
}
