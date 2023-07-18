<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Wallet\MovementType;

class MovementTypesController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('wallet.movement-types.index');
    }

    public function getMovementTypes(){

        $steps = MovementType::getAllMovementTypes();

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
            MovementType::create($data);
        }else{
            $step = MovementType::findOrFail($request->id);
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
        $step = MovementType::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $step
        ]);
    }
}
