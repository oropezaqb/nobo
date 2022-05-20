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
        return view('vouchers.edit',
            compact('voucher', 'header'));
    }
}
