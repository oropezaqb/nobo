<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payee;
use PDO;
use App\EPMADD\DbAccess;
use App\EPMADD\Report;
use DateTime;
use Dompdf\Dompdf;
use App\Http\Requests\GenerateReport;

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
    protected $vouchersForApprovalQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start, bills.period_end,
        bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
        reviewed_vouchers.remarks as reviewed_vouchers_remarks, reviewed_vouchers.endorsed_at as reviewed_vouchers_endorsed_at from reviewed_vouchers
        left join vouchers on reviewed_vouchers.voucher_id = vouchers.id
        left join bills on vouchers.bill_id = bills.id
        left join approved_vouchers on vouchers.id = approved_vouchers.voucher_id
        left join payees on bills.payee_id = payees.id
        where bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00')) and approved_vouchers.voucher_id is null";
    protected $vouchersForHOEndorsementQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
        reviewed_vouchers.remarks as reviewed_vouchers_remarks, reviewed_vouchers.endorsed_at as reviewed_vouchers_endorsed_at, approved_vouchers.approved_at,
        approved_vouchers.remarks as approved_vouchers_remarks, approved_vouchers.endorsed_at as approved_vouchers_endorsed_at from approved_vouchers
        left join vouchers on approved_vouchers.voucher_id = vouchers.id
        left join bills on vouchers.bill_id = bills.id
        left join payees on bills.payee_id = payees.id
        left join reviewed_vouchers on vouchers.id = reviewed_vouchers.voucher_id
        where bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00')) and approved_vouchers.endorsed_at is null";
    protected $vouchersForBankEndorsementQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
        reviewed_vouchers.remarks as reviewed_vouchers_remarks, reviewed_vouchers.endorsed_at as reviewed_vouchers_endorsed_at, approved_vouchers.approved_at,
        approved_vouchers.remarks as approved_vouchers_remarks, approved_vouchers.endorsed_at as approved_vouchers_endorsed_at from approved_vouchers
        left join vouchers on approved_vouchers.voucher_id = vouchers.id
        left join bills on vouchers.bill_id = bills.id
        left join reviewed_vouchers on vouchers.id = reviewed_vouchers.voucher_id
        left join bank_endorsements on vouchers.id = bank_endorsements.voucher_id
        left join payees on bills.payee_id = payees.id
        where bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00')) and bank_endorsements.voucher_id is null";
    protected $vouchersForPaymentQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start, bills.period_end,
        bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
        reviewed_vouchers.remarks as reviewed_vouchers_remarks, reviewed_vouchers.endorsed_at as reviewed_vouchers_endorsed_at, approved_vouchers.approved_at,
        approved_vouchers.remarks as approved_vouchers_remarks, approved_vouchers.endorsed_at as approved_vouchers_endorsed_at,
        bank_endorsements.approved_at as bank_endorsements_approved_at, bank_endorsements.endorsed_at as bank_endorsements_endorsed_at,
        bank_endorsements.remarks as bank_endorsements_remarks, payments.paid_at, payments.cleared_at, payments.remarks as payments_remarks from bank_endorsements
        left join vouchers on bank_endorsements.voucher_id = vouchers.id
        left join bills on vouchers.bill_id = bills.id
        left join payments on vouchers.id = payments.voucher_id
        left join approved_vouchers on vouchers.id = approved_vouchers.voucher_id
        left join reviewed_vouchers on vouchers.id = reviewed_vouchers.voucher_id
        left join payees on bills.payee_id = payees.id
        where bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00')) and payments.paid_at is null";
    protected $currentAccountsPayableQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
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
        where bills.due_at >=date(convert_tz(now(),'+00:00','+08:00')) and bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00'))
        and petty=0 and payments.paid_at is null";
    protected $accountsPayableThirtyQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
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
        where due_at>=date(convert_tz(date_sub(now(), interval 30 day),'+00:00','+08:00')) and due_at<date(convert_tz(now(),'+00:00','+08:00'))
        and petty=0 and payments.paid_at is null";
    protected $accountsPayableSixtyQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
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
        where due_at>=date(convert_tz(date_sub(now(), interval 60 day),'+00:00','+08:00'))
        and due_at<date(convert_tz(date_sub(now(), interval 30 day),'+00:00','+08:00')) and petty=0 and payments.paid_at is null";
    protected $accountsPayableNinetyQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
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
        where due_at>=date(convert_tz(date_sub(now(), interval 90 day),'+00:00','+08:00'))
        and due_at<date(convert_tz(date_sub(now(), interval 60 day),'+00:00','+08:00')) and petty=0 and payments.paid_at is null";
    protected $accountsPayableNinetyplusQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
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
        where due_at<date(convert_tz(date_sub(now(), interval 90 day),'+00:00','+08:00')) and petty=0 and payments.paid_at is null";
    protected $pettyCurrentQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
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
        where bills.due_at >=date(convert_tz(now(),'+00:00','+08:00')) and bills.due_at<=date(convert_tz(date_add(now(), interval 30 day),'+00:00','+08:00'))
        and petty=1 and payments.paid_at is null";
    protected $pettySevenQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start, bills.period_end,
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
        where due_at>=date(convert_tz(date_sub(now(), interval 7 day),'+00:00','+08:00')) and due_at<date(convert_tz(now(),'+00:00','+08:00'))
        and petty=1 and payments.paid_at is null";
    protected $pettyFourteenQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start, bills.period_end,
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
        where due_at>=date(convert_tz(date_sub(now(), interval 14 day),'+00:00','+08:00'))
        and due_at<date(convert_tz(date_sub(now(), interval 7 day),'+00:00','+08:00')) and petty=1 and payments.paid_at is null";
    protected $pettyTwentyOneQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
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
        where due_at>=date(convert_tz(date_sub(now(), interval 21 day),'+00:00','+08:00'))
        and due_at<date(convert_tz(date_sub(now(), interval 14 day),'+00:00','+08:00')) and petty=1 and payments.paid_at is null";
    protected $pettyTwentyOnePlusQuery = "select bills.received_at, payees.name, bills.amount, bills.bill_number, bills.po_number, bills.period_start,
        bills.period_end, bills.due_at, bills.endorsed_at, vouchers.remarks, vouchers.number, vouchers.posted_at, vouchers.endorsed_at as vouchers_endorsed_at,
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
        where due_at<date(convert_tz(date_sub(now(), interval 21 day),'+00:00','+08:00')) and petty=1 and payments.paid_at is null";
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }
    public function index()
    {
        $payees = Payee::latest()->get();
        $header = "Reports";
        return view('reports.index', compact('payees', 'header'));
    }
    public function billsForPayment(GenerateReport $request)
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
    public function billsForPaymentCSV(GenerateReport $request)
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
    public function vouchersForApproval()
    {
        $header = "Vouchers for Approval (Current and Past Due)";
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForApprovalQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function vouchersForApprovalCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForApprovalQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function vouchersForHOEndorsement()
    {
        $header = "Vouchers for Endorsement to HO (Current and Past Due)";
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForHOEndorsementQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function vouchersForHOEndorsementCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForHOEndorsementQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function vouchersForBankEndorsement()
    {
        $header = "Vouchers for Endorsement to Bank (Current and Past Due)";
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForBankEndorsementQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function vouchersForBankEndorsementCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForBankEndorsementQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function vouchersForPayment()
    {
        $header = "Vouchers for Payment (Current and Past Due)";
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForPaymentQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function vouchersForPaymentCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->vouchersForPaymentQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function currentAccountsPayable()
    {
        $header = "Accounts Payable (excluding SELRs) - Current (Due in 30 days)";
        $db = new DbAccess();
        $stmt = $db->query($this->currentAccountsPayableQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function currentAccountsPayableCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->currentAccountsPayableQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function accountsPayableThirty()
    {
        $header = "Accounts Payable (excluding SELRs) - 1-30 days past due";
        $db = new DbAccess();
        $stmt = $db->query($this->accountsPayableThirtyQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function accountsPayableThirtyCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->accountsPayableThirtyQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function accountsPayableSixty()
    {
        $header = "Accounts Payable (excluding SELRs) - 31-60 days past due";
        $db = new DbAccess();
        $stmt = $db->query($this->accountsPayableSixtyQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function accountsPayableSixtyCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->accountsPayableSixtyQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function accountsPayableNinety()
    {
        $header = "Accounts Payable (excluding SELRs) - 61-90 days past due";
        $db = new DbAccess();
        $stmt = $db->query($this->accountsPayableNinetyQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function accountsPayableNinetyCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->accountsPayableNinetyQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function accountsPayableNinetyplus()
    {
        $header = "Accounts Payable (excluding SELRs) - 90+ days past due";
        $db = new DbAccess();
        $stmt = $db->query($this->accountsPayableNinetyplusQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function accountsPayableNinetyplusCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->accountsPayableNinetyplusQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function pettyCurrent()
    {
        $header = "PCF Replenishment/Reimbursement - Current (Due in 30 days)";
        $db = new DbAccess();
        $stmt = $db->query($this->pettyCurrentQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function pettyCurrentCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->pettyCurrentQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function pettySeven()
    {
        $header = "PCF Replenishment/Reimbursement - 1-7 days past due";
        $db = new DbAccess();
        $stmt = $db->query($this->pettySevenQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function pettySevenCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->pettySevenQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function pettyFourteen()
    {
        $header = "PCF Replenishment/Reimbursement - 8-14 days past due";
        $db = new DbAccess();
        $stmt = $db->query($this->pettyFourteenQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function pettyFourteenCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->pettyFourteenQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function pettyTwentyOne()
    {
        $header = "PCF Replenishment/Reimbursement - 15-21 days past due";
        $db = new DbAccess();
        $stmt = $db->query($this->pettyTwentyOneQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function pettyTwentyOneCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->pettyTwentyOneQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
    public function pettyTwentyOnePlus()
    {
        $header = "PCF Replenishment/Reimbursement - 21+ days past due";
        $db = new DbAccess();
        $stmt = $db->query($this->pettyTwentyOnePlusQuery);
        $ncols = $stmt->columnCount();
        $headings = array();
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
        return view('queries.run', compact('stmt', 'headings', 'header'));
    }
    public function pettyTwentyOnePlusCSV()
    {
        $db = new DbAccess();
        $stmt = $db->query($this->pettyTwentyOnePlusQuery);
        $r = new Report();
        $url = $r->csv($stmt);
        return redirect($url);
    }
}
