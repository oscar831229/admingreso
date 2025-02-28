<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin\IcmSystemConfiguration;
use App\Clases\DataTable\TableServer;
use Illuminate\Support\Facades\Validator;

class SystemConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $configuracion = IcmSystemConfiguration::count();
        $create = $configuracion == 0 ? true : false;
        $identification_document_types = getDetailDefinitions('identification_document_types');
        return view('admin.system-configuration.index', compact('create', 'identification_document_types'));
    }

    public function datatableConfiguration(Request $request){

        $param = array(
            'model'=> new IcmSystemConfiguration,
            'method_consulta'=>'getDataTable',
            'method_cantidad'=>'getCountDatatable',
            'extradata' => []
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
        $validator = Validator::make($request->all(), [
            'url_pos_system' => "required",
            'state'          => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages(),
                'data' => []
            ]);
        }


        $data = $request->all();
        $user = auth()->user();

        if ($request->hasFile('background')) {
            $image = $request->file('background');
            $imageData = base64_encode(file_get_contents($image->getRealPath()));
            $data['background'] = 'data:image/' . $image->getClientOriginalExtension() . ';base64,' . $imageData;
        }else{
            unset($data['background']);
        }

        if(isset($data['id']) && !empty($data['id'])){
            IcmSystemConfiguration::find($data['id'])->update(array_merge($data, ['user_updated' => $user->id ]));
        }else{
            IcmSystemConfiguration::create(array_merge($data, ['user_created' => $user->id ]));
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $data
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
        $ratetype = IcmSystemConfiguration::selectRaw("
            id,
            url_pos_system,
            pos_system_token,
            system_date,
            policy_enabled,
            infrastructure_code,
            system_names,
            state,
            user_created,
            user_updated,
            created_at,
            updated_at,
            query_type_category,
            company_name,
            document_type,
            identification_number,
            address,
            phone
        ")->where(['id' => $id])->first();

        return response()->json([
            'success' => true,
            'message' => '',
            'ratetype' => $ratetype
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
