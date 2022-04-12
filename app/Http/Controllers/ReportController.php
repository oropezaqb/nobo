<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payee;
use PDO;
use App\EPMADD\DbAccess;
use App\EPMADD\Report;
use DateTime;
use Dompdf\Dompdf;

class ReportController extends Controller
{
    public function index()
    {
        $payees = Payee::latest()->get();
        $header = "Reports";
        return view('reports.index', compact('payees', 'header'));
    }
    public function billsForPayment(Request $request)
    {
        $header = "Bills for Payment (Current and Past Due) per Payee";
        $db = new DbAccess();
        $payee = Payee::all()->find(request('payee_id'));
        $stmt = $db->query("select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start, bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at, reviewed_vouchers.remarks as reviewed_vouchers_remarks, reviewed_vouchers.endorsed_at as reviewed_vouchers_endorsed_at, approved_vouchers.approved_at, approved_vouchers.remarks as approved_vouchers_remarks, approved_vouchers.endorsed_at as approved_vouchers_endorsed_at, bank_endorsements.approved_at as bank_endorsements_approved_at, bank_endorsements.endorsed_at as bank_endorsements_endorsed_at, bank_endorsements.remarks as bank_endorsements_remarks, payments.paid_at, payments.cleared_at, payments.remarks as payments_remarks
            from bills
            left join payees on bills.payee_id = payees.id
            left join vouchers on bills.id = vouchers.bill_id
            left join reviewed_vouchers on vouchers.id = reviewed_vouchers.voucher_id
            left join approved_vouchers on vouchers.id = approved_vouchers.voucher_id
            left join bank_endorsements on vouchers.id = bank_endorsements.voucher_id
            left join payments on vouchers.id = payments.voucher_id
            where payments.paid_at is null and payees.id=$payee->id");
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function billsForPaymentCSV(Request $request)
    {
        $db = new DbAccess();
        $stmt = $db->query();
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
}
