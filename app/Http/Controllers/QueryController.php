<?php

namespace App\Http\Controllers;

use App\Models\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Models\Permission;
use App\EPMADD\DbAccess;
use App\EPMADD\Report;
use App\Http\Requests\StoreQuery;
use DateTime;
use Dompdf\Dompdf;

class QueryController extends Controller
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
        if (empty(request('title')))
        {
            $queries = Query::latest()->get();
        }
        else
        {
            $queries = Query::where('title', 'like', '%' . request('title') . '%')->get();
        }
        $header = "Queries";
        if (\Route::currentRouteName() === 'queries.index')
        {
            \Request::flash();
        }
        return view('queries.index', compact('queries', 'header'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $header = "Add a New Query";
        $permissions = Permission::latest()->get();
        return view('queries.create', compact('header', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuery $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                $query = new Query([
                    'title' => request('title'),
                    'category' => request('category'),
                    'query' => request('query'),
                    'permission_id' => request('permission_id'),
                    'user_id' => request('user_id'),
                ]);
                $query->save();
            });
            return redirect(route('queries.index'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function show(Query $query)
    {
        $header = "Query Details";
        return view('queries.show',
            compact('query', 'header'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function edit(Query $query)
    {
        $header = "Edit Query";
        $permissions = Permission::latest()->get();
        return view('queries.edit',
            compact('query', 'header', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function update(StoreQuery $request, Query $query)
    {
        try {
            \DB::transaction(function () use ($request, $query) {
                $query->update([
                    'title' => request('title'),
                    'category' => request('category'),
                    'query' => request('query'),
                    'permission_id' => request('permission_id'),
                    'user_id' => request('user_id'),
                ]);
            });
            return redirect(route('queries.show', [$query]))->with('status', 'Query updated!');
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }

    public function run(Query $query)
    {
        $header = $query->title;
        if (stripos($query->query, 'file ') === 0) {
            return redirect(route('queries.index'))->with('status', 'Cannot run file reports here.');
        }
        else
        {
            $db = new DbAccess();
            $stmt = $db->query($query->query);
            $ncols = $stmt->columnCount();
            $headings = array();
            for ($i = 0; $i < $ncols; $i++) {
                $meta = $stmt->getColumnMeta($i);
                $headings[] = $meta['name'];
            }
            return view('queries.run', compact('query', 'stmt', 'headings', 'header'));
        }
    }

    public function csv(Query $query)
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
            return redirect($url);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function destroy(Query $query)
    {
        $authorized = \DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('users.id', auth()->user()->id)
            ->where('permissions.key', 'delete_queries')->exists();
        if ($authorized)
        {
            $query->delete();
            return redirect(route('queries.index'));
        }
        else
        {
            return back();
        }
    }
}
