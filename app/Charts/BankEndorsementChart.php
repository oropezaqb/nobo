<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\ApprovedVoucher;
use App\Models\BankEndorsement;

class BankEndorsementChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $numberOfEndorsedToHO = ApprovedVoucher::whereNotNull('endorsed_at')->count();
        $numberOfEndorsedToBank = BankEndorsement::whereNotNull('endorsed_at')->count();
        return Chartisan::build()
            ->labels(['To bank'])
            ->dataset('Endorsed', [$numberOfEndorsedToBank])
            ->dataset('For endorsement', [$numberOfEndorsedToHO]);
    }
}
