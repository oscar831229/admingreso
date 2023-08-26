<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clases\DataTable\TableServer;
use App\Models\Wallet\Movement;
use App\Models\Wallet\WalletUser;
use App\Models\Wallet\ElectricalPocket;
use App\Models\Wallet\WalletUserElectronicPockets;
use App\Models\Wallet\MovementType;

use Illuminate\Support\Facades\Crypt;

use App\Models\Wallet\Store;
use App\Clases\ElectronicWallet\MovementClass;

use Validator;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        # Tipos de movimientos.
        $movement_types = MovementType::all()->pluck('name', 'id');
        $stores = Store::all()->pluck('name', 'id');

        return view('wallet.transactions.index', compact('movement_types', 'stores'));
    }

    public function getTransactions(Request $request){

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $store_id = $request->store_id;
        $movement_type_id = $request->movement_type_id;

            
        $param = array( 
            'model'=> new Movement,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'store_id' => $store_id,
                'movement_type_id' => $movement_type_id
            ]
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $movement_type)
    {
        try {

            $movement = new MovementClass($movement_type);
            $movement_register = $movement->execute($request);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'cus' => $movement_register->cus
            ]
        ]);
       
    }

    public function validateTransaction(Request $request, $movement_type){

        try {

            $movement = new MovementClass($movement_type);
            $movement_register = $movement->validation($request);
            
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 401);
        }

        return response()->json([
            'success' => false,
            'message' => '',
            'data' => []
        ]);

    }

    public function printVoucherWallet($document_number, $cus){

        $walleteuser = WalletUser::where(['document_number' => $document_number])->first();
        if(!$walleteuser){
            return response()->json([
                'success' => false,
                'message' => 'No existe el usuario con documento de identificación '.$document_number,
                'data' => []
            ], 401);
        }

        $movement = Movement::where(['cus' => $cus, 'wallet_user_id' => $walleteuser->id ])->first();

        if(!$movement){
            return response()->json([
                'success' => false,
                'message' => 'No no existe la transacción con CUS '.$cus,
                'data' => []
            ], 401);
        }


        $view = view('wallet.print-voucher.vouchar-cus', compact('walleteuser', 'movement'));
        
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => base64_encode($view->render())
        ], 200);

    }

    public function show($movement_id){
        $movements = Movement::getMovementById($movement_id);
        $movement = Movement::find($movement_id);
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $movements,
            'tickets' => $movement->historic_movement_ticket_holder
        ]);
    }

}
