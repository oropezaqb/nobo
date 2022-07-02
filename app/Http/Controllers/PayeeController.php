<?php

namespace App\Http\Controllers;

use App\Models\Payee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use JavaScript;
use App\Http\Requests\StorePayee;
use PDO;
use App\EPMADD\DbAccess;
use DateTime;
use Dompdf\Dompdf;

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
            $payees = Payee::latest()->paginate(25);
        }
        else
        {
            $payees = Payee::where('name', 'like', '%' . request('name') . '%')->latest()->paginate(25);
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

    public function translateError($e)
    {
        switch ($e->getCode()) {
//            case '23000':
//                return "One or more of the products are already recorded.";
        }
        return $e->getMessage();
    }

    public function upload()
    {
        try {
            \DB::transaction(function () {
                $extension = request()->file('payees')->getClientOriginalExtension();
                $filename = uniqid().'.'.$extension;
                $path = request()->file('payees')->storeAs('input/payees', $filename);
                $csv = array_map('str_getcsv', file(base_path() . "/storage/app/" . $path));
                $messages = array();
                $error = false;
                $count = count($csv);
                $userID = auth()->user()->id;
                for ($row = 0; $row < $count; $row++) {
                    if ($row >= 0)
                    {
                        $name = $csv[$row][0];
                        if (is_null($name) OR \DB::table('payees')->where('name', $name)->exists())
                        {
                            $messages[] = 'Line ' . ($row + 1) . ' is blank.';
                            $error = true;
                        }
                        else
                        {
                            $payee = new Payee([
                                'name' => $name,
                                'user_id' => $userID
                            ]);
                            $payee->save();
                        }
                    }
                }
            });
            return redirect(route('payees.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e));
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

    public function export()
    {
        if (stripos($query->query, 'file ') === 0) {
            return redirect(route('queries.index'))->with('status', 'Cannot run file reports here.');
        }
        else
        {
            $db = new DbAccess();
            $stmt = $db->query($query->query);
            $r = new Report();
            $url = $r->csv($stmt);
            return view('reports.csv', compact('url'));
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
            try {
                \DB::transaction(function () use ($payee) {
                    $payee->delete();
                });
                return redirect(route('payees.index'));
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
