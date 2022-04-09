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
use App\Models\Bill;

class ReviewedVoucherController extends Controller
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
    public function upload()
    {
        try {
            \DB::transaction(function () {
                $extension = request()->file('reviewed_vouchers')->getClientOriginalExtension();
                $filename = uniqid().'.'.$extension;
                $path = request()->file('reviewed_vouchers')->storeAs('input/reviewed-vouchers', $filename);
                $csv = array_map('str_getcsv', file(base_path() . "/storage/app/" . $path));
                //$error = false;
                $count = count($csv);
                $userID = auth()->user()->id;
                for ($row = 0; $row < $count; $row++) {
                    $voucherNumber = $csv[$row][0];
                    $endorsedAt = $csv[$row][1];
                    if(!\DB::table('vouchers')->where('number', $voucherNumber)->exists() OR
                        \DB::table('vouchers')->rightJoin('reviewed_vouchers', 'vouchers.id', '=', 'reviewed_vouchers.voucher_id')
                        ->where('vouchers.number', $voucherNumber)->exists())
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Voucher number do not exist or already associated with a different reviewed voucher.';
                        //$error = true;
                    }
                    if((DateTime::createFromFormat('m/d/Y H:i:s', $endorsedAt) == true) OR ($endorsedAt == ''))
                    {
                        $this->messages[] = 'Line ' . ($row + 1) . '. Date endorsed is not valid.';
                        //$error = true;
                    }
                    if(count($this->messages) == 0){
                        $voucher = \DB::table('vouchers')->where('vouchers.number', $voucherNumber)->first();
                        $reviewedVoucher = new ReviewedVoucher([
                            'voucher_id' => $voucher->id,
                            'endorsed_at' => date("Y-m-d", strtotime($endorsedAt)),
                            'user_id' => $userID,
                        ]);
                        $reviewedVoucher->save();
                    }
                }
            });
            $messages = $this->messages;
            if(count($messages) > 0){
                $messages = $this->messages;
                $header = "Add a New Reviewed Voucher";
                $vouchers = Bill::latest()->get();
                return view('reviewed-vouchers.create', compact('header', 'vouchers', 'messages'));
            }
            else{
                return redirect(route('reviewed-vouchers.index'))->with('status', 'Reviewed vouchers saved!');
            }
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
    public function edit(ReviewedVoucher $reviewedVoucher)
    {
        $header = "Edit Reviewed Voucher";
        return view('reviewed-vouchers.edit',
            compact('reviewedVoucher', 'header'));
    }
    public function update(StoreReviewedVoucher $request, ReviewedVoucher $reviewedVoucher)
    {
        try {
            \DB::transaction(function () use ($request, $reviewedVoucher) {
                $reviewedVoucher->update([
                    'voucher_id' => request('voucher_id'),
                    'remarks' => request('remarks'),
                    'endorsed_at' => request('endorsed_at'),
                    'user_id' => request('user_id'),
                ]);
            });
            return redirect(route('reviewed-vouchers.show', [$reviewedVoucher]))->with('status', 'Reviewed voucher updated!');
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function destroy(ReviewedVoucher $reviewedVoucher)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_reviewed_vouchers')->exists();
        if ($authorized)
        {
            $reviewedVoucher->delete();
            return redirect(route('reviewed-vouchers.index'));
        }
        else
        {
            return back();
        }
    }
}
