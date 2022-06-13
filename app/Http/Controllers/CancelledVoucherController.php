<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CancelledVoucher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Voucher;
use App\EPMADD\DbAccess;
use App\Http\Requests\StoreCancelledVoucher;
use DateTime;
use Dompdf\Dompdf;

class CancelledVoucherController extends Controller
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
            $cancelledVouchers = \DB::table('cancelled_vouchers')->latest()->paginate(25);
        }
        else
        {
            $cancelledVouchers = \DB::table('cancelled_vouchers')
                ->where('number', 'like', '%' . request('number') . '%')
                ->latest()->paginate(25);
        }
        $header = "Cancelled Vouchers";
        if (\Route::currentRouteName() === 'cancelled-vouchers.index')
        {
            \Request::flash();
        }
        return view('cancelled-vouchers.index', compact('cancelledVouchers', 'header'));
    }
    public function show(CancelledVoucher $cancelledVoucher)
    {
        $voucher = $cancelledVoucher;
        $header = "Cancelled Voucher Details";
        return view('cancelled-vouchers.show',
            compact('voucher', 'header'));
    }
    public function edit(CancelledVoucher $cancelledVoucher)
    {
        $voucher = $cancelledVoucher;
        $header = "Edit Cancelled Voucher";
        return view('cancelled-vouchers.edit',
            compact('voucher', 'header'));
    }
    public function update(StoreCancelledVoucher $request, CancelledVoucher $cancelledVoucher)
    {
        $voucher = $cancelledVoucher;
        try {
            \DB::transaction(function () use ($request, $voucher) {
                $voucher->update([
                    'reason_for_cancellation' => request('reason_for_cancellation'),
                    'cancel_user_id' => request('user_id'),
                ]);
            });
            return redirect(route('cancelled-vouchers.show', [$voucher]))->with('status', 'Cancelled Voucher updated!');
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function translateError($e)
    {
        //switch ($e->getCode()) {
        //    case '23000':
        //        return "Voucher number already recorded.";
        //}
        return $e->getMessage();
    }
    public function destroy(CancelledVoucher $cancelledVoucher)
    {
        $voucher = $cancelledVoucher;
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_vouchers')->exists();
        if ($authorized)
        {
                $voucher->delete();
                return redirect(route('cancelled-vouchers.index'));
        }
        else
        {
            return back();
        }
    }
}
