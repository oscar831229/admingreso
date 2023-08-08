<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Wallet\WalletUser;
use App\Models\Wallet\WalletUserMovement;
use App\Clases\DataTable\TableServer;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

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

    public function generateToken($document_number, $email){

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
        $wallet_user->update();

        # Genera QR.

        # Notificar Genera cola de notificación
        # Plantilla 



        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $token_key
        ]);
         
    }



    // public function storeTrackingDonor(Request $request)    {

    //     # informacion de los posibles donantes

    //     \DB::beginTransaction();


    //     try {

    //         $data = $request->all();
    //         $pda_possible_donor_id = $data['pda_possible_donor_id'];

    //         # CONSULTAR PASO
    //         $step = PdaStep::findOrFail($data['step_id']);

    //         # REGISTRAR SEGUIMIENTO
    //         $data['user_created'] = auth()->user()->id;
    //         $data['new_state'] = $step->new_state;
    //         PdaPossibleDonorEvolution::create($data);

    //         $dataupdate = [
    //             'last_step_id' => $data['step_id'],
    //             'last_step_date' => now(),
    //             'user_updated' => auth()->user()->id
    //         ];

    //         if(!empty($step->new_state)){
    //             $dataupdate['state'] = $step->new_state;
    //         }

    //         PdaPossibleDonor::find($pda_possible_donor_id)->update($dataupdate);

    //         \DB::commit();

    //     } catch (\Throwable $th) {
            
    //         \DB::rollback();

    //         return response()->json([
    //             'success' => false,
    //             'message' => $th->getMessage(),
    //             'data'=> []
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => '',
    //         'data' => PdaPossibleDonorEvolution::where(['pda_possible_donor_id' => $pda_possible_donor_id])->get()
    //     ]);
    // }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $user_id = auth()->user()->id;
    //     $data = $request->all();

    //     if(empty($request->id)){
    //         $data['user_created'] = $user_id;
    //         PdaPossibleDonor::create($data);
    //     }else{
    //         $step = PdaPossibleDonor::findOrFail($request->id);
    //         $data['user_updated'] = $user_id;
    //         $step->update($data);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => '',
    //         'data' => []
    //     ]);

    // }

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

    // public function storeTrackingDocument(Request $request){

    //     $request->validate([
    //         'file' => 'required|max:2048',
    //     ]);

    //     $fileName = time().'.'.$request->file->extension(); 
    //     $extension = $request->file->extension();
    //     $request->file->move(storage_path('alertasdonantes'), $fileName);
        
    //     $path = storage_path('alertasdonantes');
    //     $user_id = isset(auth()->user()->id) ? auth()->user()->id : 1;

    //     $data = [
    //         'original_name' => $request->input('original_name'),
    //         'pda_possible_donor_id' => $request->input('pda_possible_donor_id'),
    //         'full_path_store' => $path,
    //         'store_name' => $fileName,
    //         'extension' => $extension,
    //         'user_created' => $user_id
    //     ];

    //     # Cargar documento soporte del cumplimiento.
    //     PdaPossibleDonorDocumentation::create($data);

    //     return response()->json([
    //         'success' => true,
    //         'message' => '',
    //         'data' => []
    //     ]);

    // }

    // public function getDownload($documentation_id){

    //     $document = PdaPossibleDonorDocumentation::findOrFail($documentation_id);
    //     $path = $document->full_path_store;
    //     $filename = $document->store_name;
    //     $extension = $document->extension;
    //     $name = $document->original_name;

    //     $file= $path. "/{$filename}";

    //     $headers = array(
    //         "Content-Type: application/{$extension}",
    //     );

    //     return \Response::download($file, "{$name}.{$extension}", $headers);

    // }

    // public function findWalletUser(){
        
    //     $username = request()->name;

    //     $responseusers = findWalletUsers($username);

    //     $arr = array('suggestions'=>array());

    //     foreach($responseusers as $key=>$responseuser){
            
    //         $arr['suggestions'][]= array(
    //             'value'=> trim($responseuser->name),
    //             'data'=>array(
    //                 'id'=>$responseuser->id
    //             )
    //         );
    //      }

    //     return response()->json($arr);
        

    // }

}
