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
        $header = "Edit Payee";
        return view('payees.edit',
            compact('payee', 'header'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function update(StorePayee $request, Payee $payee)
    {
        try {
            \DB::transaction(function () use ($request, $payee) {
                $payee->update([
                    'name' => request('name'),
                    'user_id' => request('user_id'),
                ]);
            });
            return redirect(route('payees.show', [$payee]))
                ->with('status', 'Payee updated!');
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payee $payee)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_payees')->exists();
        if ($authorized)
        {
            $payee->delete();
            return redirect(route('payees.index'));
        }
        else
        {
            return back();
        }
    }
}
