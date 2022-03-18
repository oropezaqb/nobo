<?php

namespace App\Http\Controllers;

use App\Models\Payee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JavaScript;
use App\Http\Requests\StorePayee;

class PayeeController extends Controller
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
        if (empty(request('name')))
        {
            $payees = Payee::simplePaginate(50);
        }
        else
        {
            $payees = Payee::where('name', 'like', '%' . request('name') . '%')->simplePaginate(50);
        }
        $header = "Payees";
        if (\Route::currentRouteName() === 'payees.index') {
            \Request::flash();
        }
        return view('payees.index', compact('payees', 'header'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $header = "Add a New Payee";
        return view('payees.create', compact('header'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayee $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                $payee = new Payee([
                    'name' => request('name'),
                    'user_id' => request('user_id'),
                ]);
                $payee->save();
            });
            return redirect(route('payees.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function show(Payee $payee)
    {
        $header = "Payee Details";
        return view('payees.show',
            compact('payee', 'header'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function edit(Payee $payee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payee $payee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payee $payee)
    {
        //
    }
}
