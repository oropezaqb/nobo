<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Bill;
use App\EPMADD\DbAccess;
use App\Http\Requests\StoreVoucher;
use DateTime;
use Dompdf\Dompdf;

class VoucherController extends Controller
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
            $vouchers = \DB::table('vouchers')->latest()->get();
        }
        else
        {
            $bills = \DB::table('vouchers')
                ->where('vouchers.number', 'like', '%' . request('number') . '%')
                ->latest()->get();
        }
        $header = "Vouchers";
        if (\Route::currentRouteName() === 'vouchers.index')
        {
            \Request::flash();
        }
        return view('vouchers.index', compact('vouchers', 'header'));
    }
    public function create()
    {
        $header = "Add a New Voucher";
        $bills = Bill::latest()->get();
        $bill = null;
        return view('vouchers.create', compact('header', 'bills', 'bill'));
    }
    public function process(Request $request)
    {
        $bill = Bill::find(request('bill_id'));
        $header = "Add a New Voucher";
        $bills = Bill::latest()->get();
        return view('vouchers.create', compact('header', 'bills', 'bill'));
    }
    public function store(StoreVoucher $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                $voucher = new Voucher([
                    'number' => request('number'),
                    'bill_id' => request('bill_id'),
                    'date' => request('date'),
                    'posted_at' => request('posted_at'),
                    'payable_amount' => request('payable_amount'),
                    'remarks' => request('remarks'),
                    'period_end' => request('period_end'),
                    'user_id' => request('user_id'),
                ]);
                $voucher->save();
            });
            return redirect(route('vouchers.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function getbill(Request $request)
    {
        $id = $request->input('bill_id');
        $bill = Bill::where('id', $id)->first();
        if (is_null($bill)) {
            return response()->json(array('bill' => null, 'payeename' => null,
            'billnumber' => null, 'periodstart' => null,
            'periodend' => null, 'particulars' => null,
            'amount' => null), 200);
        }
        $payee = $bill->payee;
        return response()->json(array('bill' => $bill, 'payeename' => $payee->name,
            'billnumber' => $bill->bill_number, 'periodstart' => $bill->period_start,
            'periodend' => $bill->period_end, 'particulars' => $bill->particulars,
            'amount' => $bill->amount
            ), 200);
    }
}
