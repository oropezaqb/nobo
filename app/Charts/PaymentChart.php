<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\BankEndorsement;
use DateTime;
use DateTimeZone;
use DateInterval;

class PaymentChart extends BaseChart
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
        $numberOfEndorsedToBank = \DB::table('bank_endorsements')
            ->leftJoin('vouchers', 'bank_endorsements.voucher_id', '=', 'vouchers.id')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->whereNotNull('bank_endorsements.endorsed_at')
            ->count();
        $numberOfPaidVoucher = \DB::table('payments')
            ->leftJoin('vouchers', 'payments.voucher_id', '=', 'vouchers.id')
            ->leftJoin('bills', 'vouchers.bill_id', '=', 'bills.id')
            ->where('bills.due_at', '<=', $dueDate)
            ->whereNotNull('payments.paid_at')
            ->count();
        return Chartisan::build()
            ->labels(['Payments'])
            ->dataset('Paid', [$numberOfPaidVoucher])
            ->dataset('For payment', [$numberOfEndorsedToBank]);
    }
}
