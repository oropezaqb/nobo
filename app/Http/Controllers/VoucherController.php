<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Bill;
use App\Models\CancelledVoucher;
use App\EPMADD\DbAccess;
use App\Http\Requests\StoreVoucher;
use DateTime;
use Dompdf\Dompdf;

class VoucherController extends Controller
{
    protected $messages = array();
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }
    public function index()
    {
        if (empty(request('number')))
        {
            $vouchers = \DB::table('vouchers')->latest()->paginate(25);
        }
        else
        {
            $vouchers = \DB::table('vouchers')
                ->where('vouchers.number', 'like', '%' . request('number') . '%')
                ->latest()->paginate(25);
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
        $messages = array();
        $header = "Add a New Voucher";
        $bills = Bill::latest()->get();
        return view('vouchers.create', compact('header', 'bills', 'messages'));
    }
    public function store(StoreVoucher $request)
    {
        try {
            $billAmount = Bill::find(request('bill_id'))->amount;
            if (request('payable_amount') > $billAmount)
            {
                return back()->with('status', 'Payable amount cannnot be greater than ' . $billAmount . '.')->withInput();
            }
            \DB::transaction(function () use ($request) {
                $voucher = new Voucher([
                    'number' => request('number'),
                    'bill_id' => request('bill_id'),
                    'date' => request('date'),
                    'posted_at' => request('posted_at'),
                    'payable_amount' => request('payable_amount'),
                    'remarks' => request('remarks'),
                    'endorsed_at' => request('endorsed_at'),
                    'user_id' => request('user_id'),
                ]);
                $voucher->save();
            });
            return redirect(route('vouchers.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function translateError($e)
    {
        switch ($e->getCode()) {
            case '23000':
                return "Voucher number already recorded.";
        }
        return $e->getMessage();
    }
    public function upload()
    {
        try {
            \DB::transaction(function () {
                $extension = request()->file('vouchers')->getClientOriginalExtension();
                $filename = uniqid().'.'.$extension;
                $path = request()->file('vouchers')->storeAs('input/vouchers', $filename);
                $csv = array_map('str_getcsv', file(base_path() . "/storage/app/" . $path));
                $error = false;
                $count = count($csv);
                $userID = auth()->user()->id;
                for ($row = 0; $row < $count; $row++) {
                    $voucherNumber = $csv[$row][0];
                    $billID = $csv[$row][1];
                    $voucherDate = $csv[$row][2];
                    $postingDate = $csv[$row][3];
                    $payableAmount = $csv[$row][4];
                    $endorsedAt = $csv[$row][5];
                    if(!is_numeric($voucherNumber))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Voucher number must be numeric.';
                        $error = true;
                    }
                    if(!\DB::table('bills')->where('id', $billID)->exists() OR \DB::table('vouchers')->where('bill_id', $billID)->exists())
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Bill ID do not exist or already associated with a different voucher.';
                        $error = true;
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $voucherDate) == true) OR ($voucherDate == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Voucher date is not valid.';
                        $error = true;
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $postingDate) == true) OR ($postingDate == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Posting date is not valid.';
                        $error = true;
                    }
                    if(!is_numeric($payableAmount))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Payable amount must be numeric.';
                        $error = true;
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $endorsedAt) == true) OR ($endorsedAt == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Date endorsed is not valid.';
                        $error = true;
                    }
                    if($error == false){
                        $voucher = new Voucher([
                            'number' => $voucherNumber,
                            'bill_id' => $billID,
                            'date' => date("Y-m-d", strtotime($voucherDate)),
                            'posted_at' => date("Y-m-d", strtotime($postingDate)),
                            'payable_amount' => $payableAmount,
                            'remarks' => '',
                            'endorsed_at' => date("Y-m-d", strtotime($endorsedAt)),
                            'user_id' => $userID,
                        ]);
                        $voucher->save();
                    }
                }
            });
            $messages = $this->messages;
            if(count($messages) > 0){
                $messages = $this->messages;
                $header = "Add a New Voucher";
                $bills = Bill::latest()->get();
                return view('vouchers.create', compact('header', 'bills', 'messages'));
            }
            else{
                return redirect(route('vouchers.index'))->with('status', 'Vouchers saved!');
            }
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function show(Voucher $voucher)
    {
        $header = "Voucher Details";
        return view('vouchers.show',
            compact('voucher', 'header'));
    }
    public function edit(Voucher $voucher)
    {
        $header = "Edit Voucher";
        return view('vouchers.edit',
            compact('voucher', 'header'));
    }
    public function update(StoreVoucher $request, Voucher $voucher)
    {
        try {
            \DB::transaction(function () use ($request, $voucher) {
                $voucher->update([
                    'number' => request('number'),
                    'bill_id' => request('bill_id'),
                    'date' => request('date'),
                    'posted_at' => request('posted_at'),
                    'payable_amount' => request('payable_amount'),
                    'remarks' => request('remarks'),
                    'endorsed_at' => request('endorsed_at'),
                    'user_id' => request('user_id'),
                ]);
            });
            return redirect(route('vouchers.show', [$voucher]))->with('status', 'Voucher updated!');
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
    public function destroy(Voucher $voucher)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_vouchers')->exists();
        if ($authorized)
        {
            try {
                \DB::transaction(function () use ($voucher) {
                    $voucher->delete();
                });
                return redirect(route('vouchers.index'));
            } catch (\Exception $e) {
                return back()->with('status', $this->translateError($e))->withInput();
            }
        }
        else
        {
            return back();
        }
    }
    public function cancel(Request $request, Voucher $voucher)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'edit_vouchers')->exists();
        if ($authorized)
        {
            try {
                \DB::transaction(function () use ($request, $voucher) {
                    $cancelledVoucher = new CancelledVoucher([
                        'number' => $voucher->number,
                        'bill_id' => $voucher->bill_id,
                        'date' => $voucher->date,
                        'posted_at' => $voucher->posted_at,
                        'payable_amount' => $voucher->payable_amount,
                        'remarks' => $voucher->remarks,
                        'endorsed_at' => $voucher->endorsed_at,
                        'user_id' => $voucher->user_id,
                        'reason_for_cancellation' => request('reason_for_cancellation'),
                        'cancel_user_id' => auth()->user()->id,
                    ]);
                    $cancelledVoucher->save();
                    $voucher->delete();
                });
                return redirect(route('vouchers.index'));
            } catch (\Exception $e) {
                return back()->with('status', $this->translateError($e))->withInput();
            }
        }
        else
        {
            return back();
        }
    }
}
