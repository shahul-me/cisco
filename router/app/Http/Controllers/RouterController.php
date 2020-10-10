<?php

namespace App\Http\Controllers;

use App\models\Router;
use DataTables;
use Illuminate\Http\Request;
use Response;

class RouterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Router::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $action = '<a class="btn btn-info" id="show-user" data-toggle="modal" data-id=' . $row->id . '>Show</a>
            <a class="btn btn-success" id="edit-user" data-toggle="modal" data-id=' . $row->id . '>Edit </a>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <a id="delete-user" data-id=' . $row->id . ' class="btn btn-danger delete-user">Delete</a>';

                    return $action;

                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('router');
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
        $r = $request->validate([
            'sapid' => 'unique:routers,sapid',
            'hostname' => 'unique:routers,hostname',
            'loopback' => 'unique:routers,loopback',
            'mac' => 'unique:routers,mac',
            'type' => 'required',
            

        ]);

        $uId = $request->user_id;
        Router::updateOrCreate(['id' => $uId], ['sapid' => $request->sapid, 'hostname' => $request->hostname,'loopback' => $request->loopback, 'mac' => $request->mac, 'type' => $request->type]);
        if (empty($request->user_id)) {
            $msg = 'Router created successfully.';
        } else {
            $msg = 'Router data is updated successfully';
        }

        return redirect()->route('router.index')->with('success', $msg);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $where = array('id' => $id);
        $router = Router::where($where)->first();
        return Response::json($router);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $where = array('id' => $id);
        $router = Router::where($where)->first();
        return Response::json($router);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $router = Router::where('id', $id)->delete();
        return Response::json($router);
    }
}
