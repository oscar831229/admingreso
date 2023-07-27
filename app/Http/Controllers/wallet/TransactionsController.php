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
    public function store(Request $request, $movement_type)
    {
        

        
        

        # dd($movement_register);
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
            'success' => false,
            'message' => '',
            'data' => [
                'cus' => $movement_register->cus
            ]
        ]);

        exit;

        dd($movement_type);
        
        # VERIFICAR DATOS DEL CLIENTE
        // if(!$request->has('customer')){
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No exiten datos del cliente',
        //         'data' => []
        //     ]);
        // }

        # TIPO DE MOVIMIENTO
        // $request_movement = (Object) $request->transaction;
        // $movement_type = MovementType::where(['code' => $request_movement->movement_type])->first();
        // if(!$movement_type){
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Tipo de movimiento no existente.',
        //         'data' => []
        //     ], 401);
        // }

        # VERIFICAR CUENTA CLIENTE EXISTE
       

        
        // # VERIFICAR TOKEN SOBRE USURIO.
        // // $passwordencryt = Crypt::encryptString($request->token);
        // // dd($passwordencryt);
        // $token = Crypt::decryptString($wallet_user->token);


        // if($request->token != $token){
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Error en validación de token.',
        //         'data' => []
        //     ], 401);
        // }


        # USUARIO
        $user = auth()->user();
        
        switch ($movement_type->code) {
            # ABONO
            case '01':

                # VALIDAR EXISTA BOLSILLO
                $electrical_pocket = ElectricalPocket::where(['code' => $request_movement->electrical_pocket])->first();
                if(!$electrical_pocket){
                    return response()->json([
                        'success' => false,
                        'message' => 'Código de cuenta no valido.',
                        'data' => []
                    ], 401);
                }

                # VERIFICA CODIGO DE TIENDA Y PERMISOS AL USUARIO PARA REGISTRAR
                $store = $user->stores()->where(['code' => $request_movement->store])->first();
                if(!$store){
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no tiene permisos sobre el comercio.',
                        'data' => []
                    ], 401);
                }

                # VALOR - CODIGO USUARI PROCESO.
                $validator = Validator::make($request->all(),[
                    'transaction.user_code' => 'required',
                    'transaction.value' => 'required|numeric'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error información requerida en el proceso',
                        'data' => $validator->errors()
                    ], 401);
                }

                # VERIFICAR Y CREAR BOLSILLO
                $electronic_pocket = $wallet_user->ElectronicPockets()->where(['code' => $electrical_pocket->code ])->first();
                if(!$electronic_pocket){
                    
                    # CREAMOS EL BOLSILLO
                    $electronic_pocket = $wallet_user->ElectronicPockets()->attach($electrical_pocket, [
                        'balance' => 0,
                        'last_movement_date' => date('Y-m-d'),
                        'user_created' => auth()->user()->id
                    ]);

                    # CONSULTAMOS EL BOLSILLO CREADO
                    $electronic_pocket = $wallet_user->ElectronicPockets()->where(['code' => $electrical_pocket->code ])->first();

                }

                # GENERAR CUSSS
                $identificador = generarCodigo(8);

                $electrical_pocket_wallet_user_id = $electronic_pocket->pivot->id;

                # CREAR MOVIMIENTO
                Movement::create([
                    'electrical_pocket_wallet_user_id' => $electronic_pocket->pivot->id,
                    'wallet_user_id' => $electronic_pocket->pivot->wallet_user_id,
                    'electrical_pocket_id' => $electronic_pocket->id,
                    'movement_type_id' => $movement_type->id,
                    'nature_movement' => $movement_type->nature_movement,
                    'value' =>  $request_movement->value,
                    'user_code' => $request_movement->user_code,
                    'store_id' => $store->id,
                    'cus' => $identificador ,
                    'movement_date' => date('Y-m-d'),
                    'user_created' => $user->id
                ]);
           
                break;
            # REVERSON ABONO
            case '02':

                # VERIFICAR QUE EL CUS Y EL USUARIO SEAN VALIDOS EXISTAN EN MOVIMIENTOS.
                $validator = Validator::make($request->all(),[
                    'transaction.user_code' => 'required',
                    'transaction.cus_transaction' => 'required'
                ]);

                if ($validator->fails()) {
                    return response()->json([ 
                        'success' => false,
                        'message' => 'Error información requerida en el proceso',
                        'data' => $validator->errors()
                    ], 401);
                }
                

                # CODIGO USUARI PROCESO
                $movement = Movement::where(['cus' => $request_movement->cus_transaction, 'wallet_user_id' => $wallet_user->id ])->first();

                # VALIDAR QUE CUS EXISTA.
                if(!$movement){
                    return response()->json([ 
                        'success' => false,
                        'message' => 'No existe el código CUS del consumo a reversar',
                        'data' => $validator->errors()
                    ], 401);
                }

                if($movement->movement_type->code != '01'){
                    return response()->json([ 
                        'success' => false,
                        'message' => 'Tipo de transacción a reversar invalido',
                        'data' => $validator->errors()
                    ], 401);
                }

                # VERIFICAR QUE NO EXISTA REVERSO SOBRE TRANSACCIÓN
                $movement_exists = Movement::where(['cus_transaction' => $request_movement->cus_transaction, 'wallet_user_id' => $wallet_user->id ])->first();
                if($movement_exists){
                    return response()->json([ 
                        'success' => false,
                        'message' => 'Reverso ya se encuentra aplicado.',
                        'data' => []
                    ], 401);
                }

                # GENERAR CUSSS
                $identificador = generarCodigo(8);

                $electrical_pocket_wallet_user_id = $movement->electrical_pocket_wallet_user_id;

                # CREAR MOVIMIENTO
                Movement::create([
                    'electrical_pocket_wallet_user_id' => $electrical_pocket_wallet_user_id,
                    'wallet_user_id' => $movement->wallet_user_id,
                    'electrical_pocket_id' => $movement->electrical_pocket_id,
                    'movement_type_id' => $movement_type->id,
                    'nature_movement' => $movement_type->nature_movement,
                    'value' =>  $movement->value,
                    'user_code' => $request_movement->user_code,
                    'store_id' => $movement->store_id,
                    'cus' => $identificador,
                    'cus_transaction' => $movement->cus,
                    'movement_date' => date('Y-m-d'),
                    'user_created' => $user->id
                ]);
                
                break;
            # CONSUMO
            case '03':

                # VERIFICAR TOKEN TRANSACCION CONSUMO
                $token = Crypt::decryptString($wallet_user->token);
                if($request_movement->token != $token){
                    return response()->json([
                        'success' => false,
                        'message' => 'Error en validación de token.',
                        'data' => []
                    ], 401);
                }
                
                # VALIDAR EXISTA BOLSILLO
                $electrical_pocket = ElectricalPocket::where(['code' => $request_movement->electrical_pocket])->first();
                if(!$electrical_pocket){
                    return response()->json([
                        'success' => false,
                        'message' => 'Código de cuenta INVALIDO',
                        'data' => []
                    ], 401);
                }

                

                # VERIFICA CODIGO DE TIENDA Y PERMISOS AL USUARIO PARA REGISTRAR
                $store = $user->stores()->where(['code' => $request_movement->store])->first();
                if(!$store){
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no tiene permisos sobre el comercio.',
                        'data' => []
                    ], 401);
                }

                # VALOR - CODIGO USUARI PROCESO.
                $validator = Validator::make($request->all(),[
                    'transaction.user_code' => 'required',
                    'transaction.value' => 'required|numeric'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error información requerida en el proceso',
                        'data' => $validator->errors()
                    ], 401);
                }


                # VALIDAR EL USUARIO TENGA EL BOLSILLO A PROCESAR
                $electronic_pocket = $wallet_user->ElectronicPockets()->where(['code' => $electrical_pocket->code ])->first();
                if(!$electronic_pocket){
                    return response()->json([
                        'success' => false,
                        'message' => 'Cuentra de transacción invalida',
                        'data' => $validator->errors()
                    ], 401);
                }

                

                # VERIFICAR SALDO
                $balance = $electronic_pocket->pivot->balance;

                if($request_movement->value > $balance){
                    return response()->json([
                        'success' => false,
                        'message' => 'No existe saldo suficiente para realizar operación',
                        'data' => []
                    ], 401);
                }

                # GENERAR CUSSS
                $identificador = generarCodigo(8);

                $electrical_pocket_wallet_user_id = $electronic_pocket->pivot->id;

                # CREAR MOVIMIENTO
                Movement::create([
                    'electrical_pocket_wallet_user_id' => $electrical_pocket_wallet_user_id,
                    'wallet_user_id' => $electronic_pocket->pivot->wallet_user_id,
                    'electrical_pocket_id' => $electronic_pocket->id,
                    'movement_type_id' => $movement_type->id,
                    'nature_movement' => $movement_type->nature_movement,
                    'value' =>  $request_movement->value,
                    'user_code' => $request_movement->user_code,
                    'store_id' => $store->id,
                    'cus' => $identificador ,
                    'movement_date' => date('Y-m-d'),
                    'user_created' => $user->id
                ]);

                
                break;
            # REVERSO CONSUMO
            case '04':

                # VERIFICAR QUE EL CUS Y EL USUARIO SEAN VALIDOS EXISTAN EN MOVIMIENTOS.
                $validator = Validator::make($request->all(),[
                    'transaction.user_code' => 'required',
                    'transaction.cus_transaction' => 'required'
                ]);

                if ($validator->fails()) {
                    return response()->json([ 
                        'success' => false,
                        'message' => 'Error información requerida en el proceso',
                        'data' => $validator->errors()
                    ], 401);
                }

                # CODIGO USUARI PROCESO
                $movement = Movement::where(['cus' => $request_movement->cus_transaction, 'wallet_user_id' => $wallet_user->id ])->first();

                # VALIDAR QUE CUS EXISTA.
                if(!$movement){
                    return response()->json([ 
                        'success' => false,
                        'message' => 'No existe el código CUS del consumo a reversar',
                        'data' => $validator->errors()
                    ], 401);
                }


                if($movement->movement_type->code != '03'){
                    return response()->json([ 
                        'success' => false,
                        'message' => 'Tipo de transacción a reversar invalido',
                        'data' => $validator->errors()
                    ], 401);
                }

                # VERIFICAR QUE NO EXISTA REVERSO SOBRE TRANSACCIÓN
                $movement_exists = Movement::where(['cus_transaction' => $request_movement->cus_transaction, 'wallet_user_id' => $wallet_user->id ])->first();
                if($movement_exists){
                    return response()->json([ 
                        'success' => false,
                        'message' => 'Reverso ya se encuentra aplicado.',
                        'data' => []
                    ], 401);
                }

                # GENERAR CUSSS
                $identificador = generarCodigo(8);

                $electrical_pocket_wallet_user_id = $movement->electrical_pocket_wallet_user_id;

                # CREAR MOVIMIENTO
                Movement::create([
                    'electrical_pocket_wallet_user_id' => $electrical_pocket_wallet_user_id,
                    'wallet_user_id' => $movement->wallet_user_id,
                    'electrical_pocket_id' => $movement->electrical_pocket_id,
                    'movement_type_id' => $movement_type->id,
                    'nature_movement' => $movement_type->nature_movement,
                    'value' =>  $movement->value,
                    'user_code' => $request_movement->user_code,
                    'store_id' => $movement->store_id,
                    'cus' => $identificador,
                    'cus_transaction' => $movement->cus,
                    'movement_date' => date('Y-m-d'),
                    'user_created' => $user->id
                ]);
                
                
                break;
            
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Movimiento no definido'
                ]);
                break;
        }

        # ACTUALIZAR SALDO SOBRE LA CUENTA
        $queryUpdate = "UPDATE electrical_pocket_wallet_user AS epwu 
        SET balance = IFNULL(
        (SELECT SUM( 
             CASE
                 WHEN m.nature_movement = 'C' THEN m.value
                 ELSE -m.value
             END
            ) AS saldo FROM movements AS m WHERE m.electrical_pocket_wallet_user_id = epwu.id), 0
        )
        WHERE id = ?";

        \DB::update($queryUpdate, [$electrical_pocket_wallet_user_id]);

        # NOTIFICAR ALERTA


        return response()->json([
            'success' => true,
            'message' => 'Transacción procesada de forma exitosa',
            'data' => [
                'cus' => $identificador
            ]
        ]);

       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }
}
