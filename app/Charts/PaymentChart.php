<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\BankEndorsement;

class PaymentChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $numberOfEndorsedToBank = BankEndorsement::whereNotNull('endorsed_at')->count();
        $numberOfPaidVoucher = Payment::whereNotNull('paid_at')->count();
        return Chartisan::build()
            ->labels(['Payments'])
            ->dataset('Paid', [$numberOfPaidVoucher])
            ->dataset('For payment', [$numberOfEndorsedToBank]);
    }
}
