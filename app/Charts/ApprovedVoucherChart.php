<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\ApprovedVoucher;

class ApprovedVoucherChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $numberOfApprovedVouchers = ApprovedVoucher::all()->count();
        $numberOfEndorsedToHO = ApprovedVoucher::whereNotNull('endorsed_at')->count();
        $numberOfForEndorsement = $numberOfApprovedVouchers - $numberOfEndorsedToHO;
        return Chartisan::build()
            ->labels(['Endorsed to HO', 'For endorsement'])
            ->dataset('Sample', [$numberOfEndorsedToHO, $numberOfForEndorsement]);
    }
}
