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
            $bankEndorsements = \DB::table('bank_endorsements')->latest()->paginate(25);
        }
        else
        {
            $bankEndorsements = \DB::table('bank_endorsements')
                ->leftJoin('vouchers', 'bank_endorsements.voucher_id', '=', 'vouchers.id')
                ->where('vouchers.number', 'like', '%' . request('number') . '%')
                ->select('bank_endorsements.*', 'vouchers.number')
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
    public function upload()
    {
        try {
            \DB::transaction(function () {
                $extension = request()->file('bank_endorsements')->getClientOriginalExtension();
                $filename = uniqid().'.'.$extension;
                $path = request()->file('bank_endorsements')->storeAs('input/bank-endorsements', $filename);
                $csv = array_map('str_getcsv', file(base_path() . "/storage/app/" . $path));
                $count = count($csv);
                $userID = auth()->user()->id;
                for ($row = 0; $row < $count; $row++) {
                    $voucherNumber = $csv[$row][0];
                    $approvedAt = $csv[$row][1];
                    $endorsedAt = $csv[$row][2];
                    if(!\DB::table('vouchers')->where('number', $voucherNumber)->exists() OR
                        \DB::table('vouchers')->rightJoin('bank_endorsements', 'vouchers.id', '=', 'bank_endorsements.voucher_id')
                        ->where('vouchers.number', $voucherNumber)->exists())
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Voucher number do not exist or already associated with a different bank endorsement.';
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $approvedAt) == true) OR ($approvedAt == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Date approved is not valid.';
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $endorsedAt) == true) OR ($endorsedAt == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Date endorsed is not valid.';
                    }
                    if(count($this->messages) == 0){
                        $voucher = \DB::table('vouchers')->where('vouchers.number', $voucherNumber)->first();
                        $bankEndorsement = new BankEndorsement([
                            'voucher_id' => $voucher->id,
                            'approved_at' => date("Y-m-d", strtotime($approvedAt)),
                            'endorsed_at' => date("Y-m-d", strtotime($endorsedAt)),
                            'user_id' => $userID,
                        ]);
                        $bankEndorsement->save();
                    }
                }
            });
            $messages = $this->messages;
            if(count($messages) > 0){
                $messages = $this->messages;
                $header = "Add a New Bank Endorsement";
                $vouchers = Voucher::latest()->get();
                return view('bank-endorsements.create', compact('header', 'vouchers', 'messages'));
            }
            else{
                return redirect(route('bank-endorsements.index'))->with('status', 'Bank endorsements saved!');
            }
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
            try {
                \DB::transaction(function () use ($bankEndorsement) {
                    $bankEndorsement->delete();
                });
                return redirect(route('bank-endorsements.index'));
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
