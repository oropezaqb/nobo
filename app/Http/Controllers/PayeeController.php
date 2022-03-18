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
        if (\Route::currentRouteName() === 'payee.index') {
            \Request::flash();
        }
        return view('payee.index', compact('payees', 'header'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function show(Payee $payee)
    {
        //
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
