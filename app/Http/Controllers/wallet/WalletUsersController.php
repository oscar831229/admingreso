<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Wallet\WalletUser;
use App\Models\Wallet\WalletUserMovement;
use App\Clases\DataTable\TableServer;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Clases\Mail\MainSendMail;

class WalletUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('wallet.wallet-users.index');
    }

    public function detailsWalletUsers(Request $request){

             
        $param = array( 
            'model'=> new WalletUser,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => []
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);


    }

    public function generateToken($document_number){

        # Buscar usuario
        $wallet_user = WalletUser::where(['document_number' => $document_number])->first();
        
        if(!$wallet_user)
            return response()->json([
                'success' => false,
                'message' => 'No existe el usuario',
                'data' => []
            ]);

        # GENERAR TOKEN 
        $token_key = Str::random(15);
        $wallet_user->token = Crypt::encryptString($token_key);

        # Genera QR.
        $qrcode_base64 = QrCode::size(300)->margin(2)->format('png')->generate($token_key);
        $wallet_user->imgqr = base64_encode($qrcode_base64);
        $wallet_user->update();

        # NOTIFICAR 
        MainSendMail::send('send_new_token', ['id' => $wallet_user->id ], [$wallet_user->email]);

        # NOTIFICACION
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $token_key
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

        $wallet_user = WalletUser::find($id);

        if(!$wallet_user){
            return response()->json([
                'success' => false,
                'message' => 'No existe el usuario requerido',
                'data' => []
            ]);
        }

        $customer = [
            'document_type' =>  $wallet_user->identification_document_type->name,
            'document_number' => $wallet_user->document_number,
            'name' => $wallet_user->first_name.' '.$wallet_user->second_name.' '.$wallet_user->first_surname.' '.$wallet_user->second_surname,
            'email' => $wallet_user->email,
            'phone' => $wallet_user->phone,
            'municipality' => '',
            'address' => 'CALLE 24 1F 36'
        ];

        $electronic_pockets = $wallet_user->ElectronicPockets()->get();
        $pocket = [];
        foreach ($electronic_pockets as $key => $electronic_pocket) {
            $pocket[] = [
                'id' => $electronic_pocket->pivot->id,
                'code' => $electronic_pocket->code,
                'name' => $electronic_pocket->name,
                'balance' => $electronic_pocket->pivot->balance,
                'last_movement_date' => $electronic_pocket->pivot->last_movement_date,
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'customer' => $customer,
                'electrical_pockets' => $pocket
            ]
        ]);

    }


    public function getTransactions(Request $request){

        $electrical_pocket_wallet_user_id = $request->electrical_pocket_wallet_user_id;
            
        $param = array( 
            'model'=> new WalletUserMovement,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => [
                'electrical_pocket_wallet_user_id' => $electrical_pocket_wallet_user_id
            ]
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);

    }
    
    public function update(Request $request, $document_number){
        
        # BUSCAR USUARIO A ACTUALIZAR
        $walletuser = WalletUser::where(['document_number' => $document_number])->first();
        if(!$walletuser){
            return response()->json([
                'success' => false,
                'message' => 'El usuario con identificación '.$document_number.' no tiene cuenta en billetera electrónica.',
                'data' => []
            ], 401);
        }

        $walletuser->update([
            'email' => $request->email,
            'phone' => $request->phone,
            'user_code_update' => $request->user_code
        ]);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => []
        ]);

    }

    public function getElectronicWalletBalance($document_number, $pocket){

        $walletuser = WalletUser::where(['document_number' => $document_number])->first();

        if(!$walletuser){
            return response()->json([
                'success' => false,
                'message' => "El usuario con número de documento {$document_number} no tiene registrado billetera electrónica",
                'data' => []
            ], 401);
        }

        $electricalpocket = $walletuser->ElectronicPockets()->where(['code' => $pocket])->first();
        if(!$electricalpocket){
            return response()->json([
                'success' => false,
                'message' => "El usuario con número de documento {$document_number} no tiene registro del bolsillo solicitado",
                'data' => []
            ], 401);
        }

        $data['electrical_pocket'] = [
            'code' => $electricalpocket->code,
            'name' => $electricalpocket->name,
            'operation_type' => $electricalpocket->operation_type,
            'unit_value' => $electricalpocket->unit_value,
            'minimum_purchase' => $electricalpocket->minimum_purchase,
            'balance' => $electricalpocket->pivot->balance,
            'last_movement_date' => $electricalpocket->pivot->last_movement_date,
        ];

        $data['ticket_holder'] = [];

        # Si el bolsillo es de ticketera
        if($electricalpocket->operation_type == 'T'){

            # TRAEMOS LOS TICKETS PENDIENTES DE REDIMIR
            $data['ticket_holder'] = $walletuser->WalletUserTicket($electricalpocket->pivot->id, 'P');

        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $data
        ]);


    }

    public function findWalletUser(){
        
        $name = request()->input('name');
        $data = findWalletUsers($name);

        $arr = array('suggestions'=>array());

        foreach($data as $key=>$value){
            
            $arr['suggestions'][]= array(
                'value'=> trim($value->name),
                'data'=>array(
                    'id'=>$value->id
                )
            );
         }

        return response()->json($arr);
    }


}
