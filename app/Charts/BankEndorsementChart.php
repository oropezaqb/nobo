<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\ApprovedVoucher;
use App\Models\BankEndorsement;
use DateTime;
use DateTimeZone;
use DateInterval;

class BankEndorsementChart extends BaseChart
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
        $numberOfEndorsedToHO = \DB::table('approved_vouchers')
            ->leftJoin('vouchers', 'approved_vouchers.voucher_id', '=', 'vouchers.id')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->whereNotNull('approved_vouchers.endorsed_at')
            ->count();
        $numberOfEndorsedToBank = \DB::table('bank_endorsements')
            ->leftJoin('vouchers', 'bank_endorsements.voucher_id', '=', 'vouchers.id')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->whereNotNull('bank_endorsements.endorsed_at')
            ->count();
        return Chartisan::build()
            ->labels(['To bank'])
            ->dataset('Endorsed', [$numberOfEndorsedToBank])
            ->dataset('For endorsement', [$numberOfEndorsedToHO]);
    }
}
