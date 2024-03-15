<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Income\IcmCompaniesAgreement;
use App\Clases\DataTable\TableServer;

class ParameterizationCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $identification_document_types = getDetailDefinitions('identification_document_types');
        return view('income.parameterization-companies.index', compact('identification_document_types'));
    }

    public function datatableParameterizationCompanies(Request $request){

        $param = array(
            'model'=> new IcmCompaniesAgreement,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => []
        );

        $tableserver = new TableServer($param);
        $datos = $tableserver->getDatos();

        return response()->json($datos);

    }

    public function findCompaniesAgreement(){

        $name = $_GET['name'];
        $companies = findCompanyAgreement($name);

        $arr = array('suggestions'=>array());

        foreach($companies as $key=>$company){

            $arr['suggestions'][]= array(
                'value'=> $company->document_number.' -- '.trim($company->name),
                'data'=>array(
                    'userid'=>$company->id
                )
            );
         }

        return response()->json($arr);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $request->has('id') ? $request->id : 0;
        $company = IcmCompaniesAgreement::find($id);
        $user_id = auth()->user()->id;

        if($company){
            $company->update([
                'phone'   => $request->phone,
                'address' => $request->address,
                'email'   => $request->email,
                'user_updated' => $user_id
            ]);
        }else{
            $data = $request->all();
            $data['user_created']  = $user_id;
            IcmCompaniesAgreement::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => []
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
        $company = IcmCompaniesAgreement::find($id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => $company
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
