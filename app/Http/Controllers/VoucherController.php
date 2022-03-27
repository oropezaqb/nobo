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
            $vouchers = \DB::table('vouchers')
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
        return view('vouchers.create', compact('header', 'bills'));
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
            $voucher->delete();
            return redirect(route('vouchers.index'));
        }
        else
        {
            return back();
        }
    }
}
