<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clases\DataTable\TableServer;
use App\Models\Wallet\Movement;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('wallet.transactions.index');
    }

    public function getTransactions(Request $request){

        $from_date = $request->from_date;
        $to_date = $request->to_date;

            
        $param = array( 
            'model'=> new Movement,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => [
                'from_date' => $from_date,
                'to_date' => $to_date
            ]
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);


    }

    public function storeTrackingDonor(Request $request)    {

        # informacion de los posibles donantes

        \DB::beginTransaction();


        try {

            $data = $request->all();
            $pda_possible_donor_id = $data['pda_possible_donor_id'];

            # CONSULTAR PASO
            $step = PdaStep::findOrFail($data['step_id']);

            # REGISTRAR SEGUIMIENTO
            $data['user_created'] = auth()->user()->id;
            $data['new_state'] = $step->new_state;
            PdaPossibleDonorEvolution::create($data);

            $dataupdate = [
                'last_step_id' => $data['step_id'],
                'last_step_date' => now(),
                'user_updated' => auth()->user()->id
            ];

            if(!empty($step->new_state)){
                $dataupdate['state'] = $step->new_state;
            }

            PdaPossibleDonor::find($pda_possible_donor_id)->update($dataupdate);

            \DB::commit();

        } catch (\Throwable $th) {
            
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data'=> []
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => PdaPossibleDonorEvolution::where(['pda_possible_donor_id' => $pda_possible_donor_id])->get()
        ]);
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
        $user_id = auth()->user()->id;
        $data = $request->all();

        if(empty($request->id)){
            $data['user_created'] = $user_id;
            PdaPossibleDonor::create($data);
        }else{
            $step = PdaPossibleDonor::findOrFail($request->id);
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
        $possibledonor = PdaPossibleDonor::where(['pda_possible_donors.id' => $id])->with([
            'possibledonorevolutions' => function($query){
                $query->selectRaw("pda_possible_donor_evolutions.*, ps.name as pda_step_name, uc.name as user_create_name, pcnd.name as pda_cause_non_donation_name")
                ->join('pda_steps as ps', 'ps.id', '=', 'pda_possible_donor_evolutions.step_id')
                ->join('users as uc', 'uc.id', '=', 'pda_possible_donor_evolutions.user_created')
                ->leftJoin('pda_cause_non_donations as pcnd', 'pcnd.id', '=', 'pda_possible_donor_evolutions.pda_cause_non_donation_id');
            },
            'possibledonordocumentations' => function($query){
                $query->selectRaw("pda_possible_donor_documentations.*, uc.name as user_created_name")
                ->join('users as uc', 'uc.id', '=', 'pda_possible_donor_documentations.user_created');
            },
            'type_document',
            'gender',
            'city_reports_alert' => function($query){
                $query->selectRaw("*, nommunicipio as name");
            },
            'pda_health_provider_unit',
            'gender',
            'user_invoice'
        ])
        ->selectRaw('pda_possible_donors.*, ur.name as user_register_name')
        ->join('users as ur', 'ur.id', '=', 'pda_possible_donors.user_created')
        ->first();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $possibledonor
        ]);

        
    }

    public function storeTrackingDocument(Request $request){

        $request->validate([
            'file' => 'required|max:2048',
        ]);

        $fileName = time().'.'.$request->file->extension(); 
        $extension = $request->file->extension();
        $request->file->move(storage_path('alertasdonantes'), $fileName);
        
        $path = storage_path('alertasdonantes');
        $user_id = isset(auth()->user()->id) ? auth()->user()->id : 1;

        $data = [
            'original_name' => $request->input('original_name'),
            'pda_possible_donor_id' => $request->input('pda_possible_donor_id'),
            'full_path_store' => $path,
            'store_name' => $fileName,
            'extension' => $extension,
            'user_created' => $user_id
        ];

        # Cargar documento soporte del cumplimiento.
        PdaPossibleDonorDocumentation::create($data);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => []
        ]);

    }

    public function getDownload($documentation_id){

        $document = PdaPossibleDonorDocumentation::findOrFail($documentation_id);
        $path = $document->full_path_store;
        $filename = $document->store_name;
        $extension = $document->extension;
        $name = $document->original_name;

        $file= $path. "/{$filename}";

        $headers = array(
            "Content-Type: application/{$extension}",
        );

        return \Response::download($file, "{$name}.{$extension}", $headers);

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
