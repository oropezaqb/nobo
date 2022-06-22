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
use DateTimeZone;
use DateInterval;

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
            $bills = \DB::table('bills')->latest()->paginate(25);
        }
        else
        {
            if (empty(request('bill_number')))
            {
                $bills = \DB::table('bills')
                    ->leftJoin('payees', 'bills.payee_id', '=', 'payees.id')
                    ->where('payees.name', 'like', '%' . request('payee') . '%')
                    ->select('bills.*', 'payees.name')
                    ->latest()->paginate(25);
            }
            else
            {
                $bills = \DB::table('bills')
                    ->leftJoin('payees', 'bills.payee_id', '=', 'payees.id')
                    ->where('payees.name', 'like', '%' . request('payee') . '%')
                    ->where('bills.bill_number', 'like', '%' . request('bill_number') . '%')
                    ->select('bills.*', 'payees.name')
                    ->latest()->paginate(25);
            }
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
                $bill = new Bill([
                    'received_at' => request('received_at'),
                    'payee_id' => request('payee_id'),
                    'amount' => request('amount'),
                    'bill_number' => request('bill_number'),
                    'billed_at' => request('billed_at'),
                    'po_number' => request('po_number'),
                    'period_start' => request('period_start'),
                    'period_end' => request('period_end'),
                    'petty' => request('petty'),
                    'classification' => request('classification'),
                    'due_at' => request('due_at'),
                    'endorsed_at' => request('endorsed_at'),
                    'particulars' => request('particulars'),
                    'remarks' => request('remarks'),
                    'user_id' => request('user_id'),
                ]);
                $bill->save();
            });
            return redirect(route('bills.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function translateError($e)
    {
        switch ($e->getCode()) {
            //case '23000':
                //return "Bill number for this supplier is already recorded.";
        }
        return $e->getMessage();
    }

    public function show(Bill $bill)
    {
        $header = "Bill Details";
        $payees = Payee::latest()->get();
        return view('bills.show',
            compact('bill', 'header', 'payees'));
    }
    public function edit(Bill $bill)
    {
        $header = "Edit Bill";
        $payees = Payee::latest()->get();
        return view('bills.edit',
            compact('bill', 'header', 'payees'));
    }
    public function update(StoreBill $request, Bill $bill)
    {
        try {
            \DB::transaction(function () use ($request, $bill) {
                $bill->update([
                    'received_at' => request('received_at'),
                    'payee_id' => request('payee_id'),
                    'amount' => request('amount'),
                    'bill_number' => request('bill_number'),
                    'billed_at' => request('billed_at'),
                    'po_number' => request('po_number'),
                    'period_start' => request('period_start'),
                    'period_end' => request('period_end'),
                    'petty' => request('petty'),
                    'classification' => request('classification'),
                    'due_at' => request('due_at'),
                    'endorsed_at' => request('endorsed_at'),
                    'particulars' => request('particulars'),
                    'remarks' => request('remarks'),
                    'user_id' => request('user_id'),
                ]);
            });
            return redirect(route('bills.show', [$bill]))->with('status', 'Bill updated!');
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
    public function destroy(Bill $bill)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_bills')->exists();
        if ($authorized)
        {
            try {
                \DB::transaction(function () use ($bill) {
                    $bill->delete();
                });
                return redirect(route('bills.index'));
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
