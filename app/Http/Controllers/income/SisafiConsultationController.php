<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Seac\ClientesSeac;
use App\Models\Income\IcmLiquidation;
use App\Clases\DataTable\TableServer;
use App\Clases\Cajasan\Parameters;

use App\Jobs\SincronizarAfiliados;
use App\Clases\Cajasan\Afiliacion;
use Illuminate\Support\Facades\Validator;

use App\Clases\Cajasan\SynchronizeAffiliates;



class SisafiConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('income.sisafi-consultation.index');
    }

    public function datatableConsultations(Request $request){

        $extradata = [];

        if(!empty($request->identificacion)){
            $extradata['identificacion'] = $request->identificacion;
        }

        if(!empty($request->id_principal)){
            $extradata['id_principal'] = $request->id_principal;
        }

        $param = array(
            'model'=> new ClientesSeac,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => $extradata
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
    public function store(Request $request)
    {
        $affiliates = $request->affiliates;

        $user_id = auth()->user()->id;

        foreach ($affiliates as $key => $affiliate) {

            $parameter = new Parameters;
            $parameter->type_document        = $affiliate['tipo_id'];
            $parameter->document_number      = $affiliate['identificacion'];
            $parameter->type_execution       = 'M';
            $parameter->type_synchronization = 'I';
            $parameter->user_id              = $user_id;

            $sincronizacion = new SynchronizeAffiliates;
            $sincronizacion->execute($parameter);

        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => []
        ]);
    }

    public function getAffiliateCategory(Request $request){

        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'required|string|in:CC,TI,PP',  // Ejemplo de tipos permitidos
            'nro_documento'  => 'required|numeric|digits_between:7,15',  // Número de documento
            'tipo'           => 'required|integer|in:1,2',  // Validación para los valores posibles de tipo
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();
            $formattedErrors = [];
            foreach ($errors->messages() as $field => $messages) {
                $formattedErrors[$field] = implode(', ', $messages);
            }

            return response()->json([
                'message' => $formattedErrors
            ], 400)->header('Content-Type', 'application/json');  // Retorna los errores con un código 422
        }

        $document_number = $request->nro_documento;
        $type_document   = $request->tipo_documento;

        $afiliacion = new Afiliacion();
        $response   = $afiliacion->consultaLocal($document_number, $type_document);

        return response()->json($response)->header('Content-Type', 'application/json');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
