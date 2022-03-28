<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\ReviewedVoucher;

class VoucherChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $numberOfVouchers = Voucher::all()->count();
        $numberOfReviewedVouchers = ReviewedVoucher::all()->count();
        $numberOfVouchersForReview = $numberOfVouchers - $numberOfReviewedVouchers;
        return Chartisan::build()
            ->labels(['Vouchers reviewed', 'Vouchers for review'])
            ->dataset('Sample', [$numberOfReviewedVouchers, $numberOfVouchersForReview]);
    }
}
