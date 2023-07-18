<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Wallet\Store;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('wallet.business.index');
    }

    public function getDetailBusiness(){

        $steps = Store::getAllBusiness();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $steps
        ]);
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = $request->all();

        if(empty($request->id)){
            $data['user_created'] = $user_id;
            Store::create($data);
        }else{
            $step = Store::findOrFail($request->id);
            $data['user_updated'] = $user_id;
            $step->update($data);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => []
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $step = Store::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $step
        ]);
    }
}
