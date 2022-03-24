<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Payee;
use App\EPMADD\DbAccess;
use App\Http\Requests\StoreBill;
use DateTime;
use Dompdf\Dompdf;

class BillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (empty(request('payee')))
        {
            $bills = Bill::latest()->get();
        }
        else
        {
            $bills = \DB::table('bills')
                ->leftJoin('payees', 'bills.payee_id', '=', 'payees.id')
                ->where('title', 'like', '%' . request('title') . '%')->get();
        }
        $header = "Bills";
        if (\Route::currentRouteName() === 'bills.index')
        {
            \Request::flash();
        }
        return view('bills.index', compact('bills', 'header'));
    }

    public function create()
    {
        $header = "Add a New Bill";
        $payees = Payee::latest()->get();
        return view('bills.create', compact('header', 'payees'));
    }

    public function store(StoreBill $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                $query = new Query([
                    'received_at' => request('received_at'),
                    'payee_id' => request('payee_id'),
                    'amount' => request('amount'),
                    'bill_number' => request('bill_number'),
                    'po_number' => request('po_number'),
                    'period_start' => request('period_start'),
                    'period_end' => request('period_end'),
                    'due_at' => request('due_at'),
                    'endorsed_at' => request('endorsed_at'),
                    'particulars' => request('particulars'),
                    'user_id' => request('user_id'),
                ]);
                $query->save();
            });
            return redirect(route('bills.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
}
