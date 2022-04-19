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
    protected $billsForProcessingQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number,
        bills.period_start, bills.period_end, bills.due_at, bills.endorsed_at from bills
        left join payees on bills.payee_id = payees.id
        left join vouchers on bills.id = vouchers.bill_id
        where bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00')) and vouchers.bill_id is null";
    protected $billsForPaymentQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start, bills.period_end,
        bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
        reviewed_vouchers.remarks as reviewed_vouchers_remarks, reviewed_vouchers.endorsed_at as reviewed_vouchers_endorsed_at, approved_vouchers.approved_at,
        approved_vouchers.remarks as approved_vouchers_remarks, approved_vouchers.endorsed_at as approved_vouchers_endorsed_at,
        bank_endorsements.approved_at as bank_endorsements_approved_at, bank_endorsements.endorsed_at as bank_endorsements_endorsed_at,
        bank_endorsements.remarks as bank_endorsements_remarks, payments.paid_at, payments.cleared_at, payments.remarks as payments_remarks
        from bills
        left join payees on bills.payee_id = payees.id
        left join vouchers on bills.id = vouchers.bill_id
        left join reviewed_vouchers on vouchers.id = reviewed_vouchers.voucher_id
        left join approved_vouchers on vouchers.id = approved_vouchers.voucher_id
        left join bank_endorsements on vouchers.id = bank_endorsements.voucher_id
        left join payments on vouchers.id = payments.voucher_id
        where bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00')) and payments.paid_at is null";
    protected $vouchersForReviewQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start, bills.period_end,
        bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at from vouchers
        left join bills on vouchers.bill_id = bills.id
        left join reviewed_vouchers on vouchers.id = reviewed_vouchers.voucher_id
        left join payees on bills.payee_id = payees.id
        where bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00')) and reviewed_vouchers.voucher_id is null";
    protected 
    public function index()
    {
        $payees = Payee::latest()->get();
        $header = "Reports";
        return view('reports.index', compact('payees', 'header'));
    }
    public function billsForPayment(Request $request)
    {
        $header = "Bills for Payment (Current and Past Due)";
        $db = new DbAccess();
        $payee = Payee::all()->find(request('payee_id'));
        if(is_null(request('payee_id'))){
            $stmt = $db->query($this->billsForPaymentQuery);
        }
        else{
            $stmt = $db->query($this->billsForPaymentQuery . " and payees.id=" . $payee->id);
        }
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
        $payee = Payee::all()->find(request('payee_id'));
        if(is_null(request('payee_id'))){
            $stmt = $db->query($this->billsForPaymentQuery);
        }
        else{
            $stmt = $db->query($this->billsForPaymentQuery . " and payees.id=" . $payee->id);
        }
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function reviewedVouchersCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query("select bills.classification, vouchers.number, vouchers.posted_at, payees.name, bills.particulars, bills.due_at, bills.bill_number,
            bills.billed_at, vouchers.payable_amount
            from reviewed_vouchers
            left join vouchers on reviewed_vouchers.voucher_id = vouchers.id
            left join bills on vouchers.bill_id = bills.id
            left join payees on bills.payee_id = payees.id
            left join approved_vouchers on vouchers.id = approved_vouchers.voucher_id
            where approved_vouchers.endorsed_at is null");
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function billsForProcessing()
    {
        $header = "Bills for Processing (Current and Past Due)";
        $db = new DbAccess();
        $stmt = $db->query($this->billsForProcessingQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function billsForProcessingCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->billsForProcessingQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function vouchersForReview()
    {
        $header = "Vouchers For Review (Current and Past Due)";
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForReviewQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function vouchersForReviewCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForReviewQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
}
