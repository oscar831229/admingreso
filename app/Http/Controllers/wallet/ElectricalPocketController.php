<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet\ElectricalPocket;
use App\Models\Wallet\WalletUserTicket;



class ElectricalPocketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('wallet.electrical-pockets.index');
    }

    public function getDetailElectricalPockets(){

        $steps = ElectricalPocket::getAllElectricalPockets();

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

        if($request->operation_type == 'C'){
            $data['minimum_purchase'] = NULL;
            $data['unit_value'] = NULL;
        }

        if(empty($request->id)){
            $data['user_created'] = $user_id;
            ElectricalPocket::create($data);
        }else{
            $step = ElectricalPocket::findOrFail($request->id);
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
        $step = ElectricalPocket::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $step
        ]);
    }

    public function getDetailElectricalPocketTickets($electrical_pocket_wallet_user_id){

        $tickets = WalletUserTicket::getEnabledTicket($electrical_pocket_wallet_user_id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $tickets
        ]);

    }

    

}
