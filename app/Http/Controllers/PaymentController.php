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
    protected $messages = array();
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }
    public function index()
    {
        if (empty(request('number')) && empty(request('check_number')))
        {
            $payments = \DB::table('payments')->latest()->paginate(25);
        }
        elseif (!empty(request('number')))
        {
            $payments = \DB::table('payments')
                ->leftJoin('vouchers', 'payments.voucher_id', '=', 'vouchers.id')
                ->where('vouchers.number', 'like', '%' . request('number') . '%')
                ->select('payments.*', 'vouchers.number')
                ->latest()->paginate(25);
        }
        else
        {
            $payments = \DB::table('payments')
                ->leftJoin('vouchers', 'payments.voucher_id', '=', 'vouchers.id')
                ->where('payments.check_number', 'like', '%' . request('check_number') . '%')
                ->select('payments.*', 'vouchers.number')
                ->latest()->paginate(25);
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
                $clearedAmount = 0;
                $serviceCharge = 0;
                if (!empty(request('cleared_amount')))
                {
                    $clearedAmount = request('cleared_amount');
                }
                if (!empty(request('service_charge')))
                {
                    $serviceCharge = request('service_charge');
                }
                $payment = new Payment([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'check_number' => request('check_number'),
                    'check_date' => request('check_date'),
                    'paid_at' => request('paid_at'),
                    'cleared_at' => request('cleared_at'),
                    'cancelled_checks' => request('cancelled_checks'),
                    'cleared_amount' => $clearedAmount,
                    'service_charge' => $serviceCharge,
                    'receipt_number' => request('receipt_number'),
                    'receipt_received_at' => request('receipt_received_at'),
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
        //switch ($e->getCode()) {
        //    case '23000':
        //        return "Voucher number already recorded.";
        //}
        return $e->getMessage();
    }
    public function upload()
    {
        try {
            \DB::transaction(function () {
                $extension = request()->file('payments')->getClientOriginalExtension();
                $filename = uniqid().'.'.$extension;
                $path = request()->file('payments')->storeAs('input/payments', $filename);
                $csv = array_map('str_getcsv', file(base_path() . "/storage/app/" . $path));
                $count = count($csv);
                $userID = auth()->user()->id;
                for ($row = 0; $row < $count; $row++) {
                    $voucherNumber = $csv[$row][0];
                    $checkNumber = $csv[$row][1];
                    $checkDate = $csv[$row][2];
                    $paidAt = $csv[$row][3];
                    $clearedAt = $csv[$row][4];
                    if(!\DB::table('vouchers')->where('number', $voucherNumber)->exists() OR
                        \DB::table('vouchers')->rightJoin('payments', 'vouchers.id', '=', 'payments.voucher_id')
                        ->where('vouchers.number', $voucherNumber)->exists())
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Voucher number do not exist or already associated with a different payment.';
                    }
                    if(DateTime::createFromFormat('m/d/Y H:i:s', $checkDate) == true)
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Check date is not valid.';
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $paidAt) == true) OR ($paidAt == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Date paid is not valid.';
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $clearedAt) == true) OR ($clearedAt == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Date cleared is not valid.';
                    }
                    if(count($this->messages) == 0){
                        $voucher = \DB::table('vouchers')->where('vouchers.number', $voucherNumber)->first();
                        $payment = new Payment([
                            'voucher_id' => $voucher->id,
                            'check_date' => date("Y-m-d", strtotime($checkDate)),
                            'paid_at' => date("Y-m-d", strtotime($paidAt)),
                            'cleared_at' => date("Y-m-d", strtotime($clearedAt)),
                            'check_number' => $checkNumber,
                            'user_id' => $userID,
                        ]);
                        $payment->save();
                    }
                }
            });
            $messages = $this->messages;
            if(count($messages) > 0){
                $messages = $this->messages;
                $header = "Add a New Payment";
                $vouchers = Voucher::latest()->get();
                return view('payments.create', compact('header', 'vouchers', 'messages'));
            }
            else{
                return redirect(route('payments.index'))->with('status', 'Payments saved!');
            }
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
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
                $clearedAmount = 0;
                $serviceCharge = 0;
                if (!empty(request('cleared_amount')))
                {
                    $clearedAmount = request('cleared_amount');
                }
                if (!empty(request('service_charge')))
                {
                    $serviceCharge = request('service_charge');
                }
                $payment->update([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'check_number' => request('check_number'),
                    'check_date' => request('check_date'),
                    'paid_at' => request('paid_at'),
                    'cleared_at' => request('cleared_at'),
                    'cancelled_checks' => request('cancelled_checks'),
                    'cleared_amount' => $clearedAmount,
                    'service_charge' => $serviceCharge,
                    'receipt_number' => request('receipt_number'),
                    'receipt_received_at' => request('receipt_received_at'),
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
