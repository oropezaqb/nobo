<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReviewedVoucher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Voucher;
use App\EPMADD\DbAccess;
use App\Http\Requests\StoreReviewedVoucher;
use DateTime;
use Dompdf\Dompdf;

class ReviewedVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }
    public function index()
    {
        if (empty(request('number')))
        {
            $reviewedVouchers = \DB::table('reviewed_vouchers')->latest()->get();
        }
        else
        {
            $vouchers = \DB::table('reviewed_vouchers')
                ->leftJoin('vouchers', 'reviewed_vouchers.voucher_id', '=', 'vouchers.id')
                ->where('voucher.number', 'like', '%' . request('number') . '%')
                ->select('reviewed_vouchers.*', 'voucher.number')
                ->latest()->get();
        }
        $header = "Reviewed Vouchers";
        if (\Route::currentRouteName() === 'reviewed-vouchers.index')
        {
            \Request::flash();
        }
        return view('reviewed-vouchers.index', compact('reviewedVouchers', 'header'));
    }
    public function create()
    {
        $header = "Add a New Reviewed Voucher";
        $vouchers = Voucher::latest()->get();
        return view('reviewed-vouchers.create', compact('header', 'vouchers'));
    }
    public function getVoucher(Request $request)
    {
        $voucherNumber = $request->input('voucher_number');
        $voucher = Voucher::where('number', $voucherNumber)->first();
        if (is_null($voucher)) {
            return response()->json(array('bill' => null, 'payeename' => null,
            'billnumber' => null, 'periodstart' => null,
            'periodend' => null, 'particulars' => null,
            'amount' => null, 'date' => null, 'postedat' => null,
            'payableamount' => null), 200);
        }
        $payee = $voucher->bill->payee;
        return response()->json(array('voucher' => $voucher, 'payeename' => $payee->name,
            'billnumber' => $voucher->bill->bill_number, 'periodstart' => $voucher->bill->period_start,
            'periodend' => $voucher->bill->period_end, 'particulars' => $voucher->bill->particulars,
            'amount' => $voucher->bill->amount, 'date' => $voucher->date, 'postedat' => $voucher->posted_at,
            'payableamount' => $voucher->payable_amount
            ), 200);
    }
    public function store(StoreReviewedVoucher $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                $reviewedVoucher = new ReviewedVoucher([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'endorsed_at' => request('endorsed_at'),
                    'user_id' => request('user_id'),
                ]);
                $reviewedVoucher->save();
            });
            return redirect(route('reviewed-vouchers.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function show(ReviewedVoucher $reviewedVoucher)
    {
        $header = "Reviewed Voucher Details";
        return view('reviewed-vouchers.show',
            compact('reviewedVoucher', 'header'));
    }
    public function translateError($e)
    {
        switch ($e->getCode()) {
            case '23000':
                return "Voucher number already recorded.";
        }
        return $e->getMessage();
    }
}
