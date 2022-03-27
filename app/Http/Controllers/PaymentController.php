<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Voucher;
use App\EPMADD\DbAccess;
use App\Http\Requests\StorePayment;
use DateTime;
use Dompdf\Dompdf;

class PaymentController extends Controller
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
            $payments = \DB::table('payments')->latest()->get();
        }
        else
        {
            $payments = \DB::table('payments')
                ->leftJoin('vouchers', 'reviewed_vouchers.voucher_id', '=', 'vouchers.id')
                ->where('voucher.number', 'like', '%' . request('number') . '%')
                ->select('reviewed_vouchers.*', 'voucher.number')
                ->latest()->get();
        }
        $header = "Payments";
        if (\Route::currentRouteName() === 'payments.index')
        {
            \Request::flash();
        }
        return view('payments.index', compact('payments', 'header'));
    }
    public function create()
    {
        $header = "Add a New Payment";
        $vouchers = Voucher::latest()->get();
        return view('payments.create', compact('header', 'vouchers'));
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
    public function store(StorePayment $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                $payment = new Payment([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'paid_at' => request('paid_at'),
                    'cleared_at' => request('cleared_at'),
                    'user_id' => request('user_id'),
                ]);
                $payment->save();
            });
            return redirect(route('payments.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function show(Payment $payment)
    {
        $header = "Payment Details";
        return view('payments.show',
            compact('payment', 'header'));
    }
    public function translateError($e)
    {
        switch ($e->getCode()) {
            case '23000':
                return "Voucher number already recorded.";
        }
        return $e->getMessage();
    }
    public function edit(Payment $payment)
    {
        $header = "Edit Payment";
        return view('payments.edit',
            compact('payment', 'header'));
    }
    public function update(StorePayment $request, Payment $payment)
    {
        try {
            \DB::transaction(function () use ($request, $payment) {
                $payment->update([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'paid_at' => request('paid_at'),
                    'cleared_at' => request('cleared_at'),
                    'user_id' => request('user_id'),
                ]);
            });
            return redirect(route('payments.show', [$payment]))->with('status', 'Payment updated!');
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function destroy(Payment $payment)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_payments')->exists();
        if ($authorized)
        {
            $payment->delete();
            return redirect(route('payments.index'));
        }
        else
        {
            return back();
        }
    }
}
