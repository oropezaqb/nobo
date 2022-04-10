<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankEndorsement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Voucher;
use App\EPMADD\DbAccess;
use App\Http\Requests\StoreBankEndorsement;
use DateTime;
use Dompdf\Dompdf;

class BankEndorsementController extends Controller
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
            $bankEndorsements = \DB::table('bank_endorsements')->latest()->paginate(25);
        }
        else
        {
            $bankEndorsements = \DB::table('bank_endorsements')
                ->leftJoin('vouchers', 'reviewed_vouchers.voucher_id', '=', 'vouchers.id')
                ->where('voucher.number', 'like', '%' . request('number') . '%')
                ->select('reviewed_vouchers.*', 'voucher.number')
                ->latest()->paginate(25);
        }
        $header = "Bank Endorsements";
        if (\Route::currentRouteName() === 'bank_endorsements.index')
        {
            \Request::flash();
        }
        return view('bank-endorsements.index', compact('bankEndorsements', 'header'));
    }
    public function create()
    {
        $header = "Add a New Bank Endorsement";
        $vouchers = Voucher::latest()->get();
        return view('bank-endorsements.create', compact('header', 'vouchers'));
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
    public function store(StoreBankEndorsement $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                $bankEndorsement = new BankEndorsement([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'approved_at' => request('approved_at'),
                    'endorsed_at' => request('endorsed_at'),
                    'user_id' => request('user_id'),
                ]);
                $bankEndorsement->save();
            });
            return redirect(route('bank-endorsements.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function show(BankEndorsement $bankEndorsement)
    {
        $header = "Bank Endorsement Details";
        return view('bank-endorsements.show',
            compact('bankEndorsement', 'header'));
    }
    public function translateError($e)
    {
        switch ($e->getCode()) {
            case '23000':
                return "Voucher number already recorded.";
        }
        return $e->getMessage();
    }
    public function edit(BankEndorsement $bankEndorsement)
    {
        $header = "Edit Bank Endorsement";
        return view('bank-endorsements.edit',
            compact('bankEndorsement', 'header'));
    }
    public function update(StoreBankEndorsement $request, BankEndorsement $bankEndorsement)
    {
        try {
            \DB::transaction(function () use ($request, $bankEndorsement) {
                $bankEndorsement->update([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'approved_at' => request('approved_at'),
                    'endorsed_at' => request('endorsed_at'),
                    'user_id' => request('user_id'),
                ]);
            });
            return redirect(route('bank-endorsements.show', [$bankEndorsement]))->with('status', 'Bank endorsement updated!');
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function destroy(BankEndorsement $bankEndorsement)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_bank_endorsements')->exists();
        if ($authorized)
        {
            $bankEndorsement->delete();
            return redirect(route('bank-endorsements.index'));
        }
        else
        {
            return back();
        }
    }
}
