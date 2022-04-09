<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovedVoucher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Voucher;
use App\EPMADD\DbAccess;
use App\Http\Requests\StoreApprovedVoucher;
use DateTime;
use Dompdf\Dompdf;

class ApprovedVoucherController extends Controller
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
            $approvedVouchers = \DB::table('approved_vouchers')->latest()->get();
        }
        else
        {
            $approvedVouchers = \DB::table('approved_vouchers')
                ->leftJoin('vouchers', 'reviewed_vouchers.voucher_id', '=', 'vouchers.id')
                ->where('voucher.number', 'like', '%' . request('number') . '%')
                ->select('reviewed_vouchers.*', 'voucher.number')
                ->latest()->get();
        }
        $header = "Approved Vouchers";
        if (\Route::currentRouteName() === 'approved-vouchers.index')
        {
            \Request::flash();
        }
        return view('approved-vouchers.index', compact('approvedVouchers', 'header'));
    }
    public function create()
    {
        $header = "Add a New Approved Voucher";
        $vouchers = Voucher::latest()->get();
        return view('approved-vouchers.create', compact('header', 'vouchers'));
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
    public function store(StoreApprovedVoucher $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                $approvedVoucher = new ApprovedVoucher([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'approved_at' => request('approved_at'),
                    'endorsed_at' => request('endorsed_at'),
                    'batch_number' => request('batch_number'),
                    'user_id' => request('user_id'),
                ]);
                $approvedVoucher->save();
            });
            return redirect(route('approved-vouchers.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function upload()
    {
        try {
            \DB::transaction(function () {
                $extension = request()->file('approved_vouchers')->getClientOriginalExtension();
                $filename = uniqid().'.'.$extension;
                $path = request()->file('approved_vouchers')->storeAs('input/approved-vouchers', $filename);
                $csv = array_map('str_getcsv', file(base_path() . "/storage/app/" . $path));
                $count = count($csv);
                $userID = auth()->user()->id;
                for ($row = 0; $row < $count; $row++) {
                    $voucherNumber = $csv[$row][0];
                    $approvedAt = $csv[$row][1];
                    $endorsedAt = $csv[$row][2];
                    $batchNumber = $csv[$row][3];
                    if(!\DB::table('vouchers')->where('number', $voucherNumber)->exists() OR
                        \DB::table('vouchers')->rightJoin('approved_vouchers', 'vouchers.id', '=', 'approved_vouchers.voucher_id')
                        ->where('vouchers.number', $voucherNumber)->exists())
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Voucher number do not exist or already associated with a different approved voucher.';
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $approvedAt) == true) OR ($approvedAt == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Date approved is not valid.';
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $endorsedAt) == true) OR ($endorsedAt == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Date endorsed is not valid.';
                    }
                    if($batchNumber == ''){
                        $this->messages[] = 'Line ' . ($row + 1) . '. Batch number is required.';
                    }
                    if(count($this->messages) == 0){
                        $voucher = \DB::table('vouchers')->where('vouchers.number', $voucherNumber)->first();
                        $approvedVoucher = new ApprovedVoucher([
                            'voucher_id' => $voucher->id,
                            'approved_at' => date("Y-m-d", strtotime($approvedAt)),
                            'endorsed_at' => date("Y-m-d", strtotime($endorsedAt)),
                            'batch_number' => $batchNumber,
                            'user_id' => $userID,
                        ]);
                        $approvedVoucher->save();
                    }
                }
            });
            $messages = $this->messages;
            if(count($messages) > 0){
                $messages = $this->messages;
                $header = "Add a New Approved Voucher";
                $vouchers = Voucher::latest()->get();
                return view('approved-vouchers.create', compact('header', 'vouchers', 'messages'));
            }
            else{
                return redirect(route('approved-vouchers.index'))->with('status', 'Approved vouchers saved!');
            }
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function show(ApprovedVoucher $approvedVoucher)
    {
        $header = "Approved Voucher Details";
        return view('approved-vouchers.show',
            compact('approvedVoucher', 'header'));
    }
    public function translateError($e)
    {
        switch ($e->getCode()) {
            case '23000':
                return "Voucher number already recorded.";
        }
        return $e->getMessage();
    }
    public function edit(ApprovedVoucher $approvedVoucher)
    {
        $header = "Edit Approved Voucher";
        return view('approved-vouchers.edit',
            compact('approvedVoucher', 'header'));
    }
    public function update(StoreApprovedVoucher $request, ApprovedVoucher $approvedVoucher)
    {
        try {
            \DB::transaction(function () use ($request, $approvedVoucher) {
                $approvedVoucher->update([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'approved_at' => request('approved_at'),
                    'endorsed_at' => request('endorsed_at'),
                    'batch_number' => request('batch_number'),
                    'user_id' => request('user_id'),
                ]);
            });
            return redirect(route('approved-vouchers.show', [$approvedVoucher]))->with('status', 'Approved voucher updated!');
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function destroy(ApprovedVoucher $approvedVoucher)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_approved_vouchers')->exists();
        if ($authorized)
        {
            $approvedVoucher->delete();
            return redirect(route('approved-vouchers.index'));
        }
        else
        {
            return back();
        }
    }
}
