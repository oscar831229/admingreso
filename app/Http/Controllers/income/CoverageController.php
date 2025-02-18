<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Income\IcmCoverage;

use App\Clases\DataTable\TableServer;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ExecuteCoverage;

class CoverageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('income.coverages.index');
    }

    public function datatableCoverages(Request $request){

        $extradata = [];

        if(!empty($request->state)){
            $extradata['state'] = $request->state;
        }

        if(!empty($request->date_from)){
            $extradata['date_from'] = $request->date_from;
        }

        if(!empty($request->date_to)){
            $extradata['date_to'] = $request->date_to;
        }

        $param = array(
            'model'=> new IcmCoverage,
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
        $date_from = $request->date_from;
        $date_to   = $request->date_to;

        $dates = $this->getDatesBetween($date_from, $date_to);
        foreach ($dates as $coverage_date) {
            ExecuteCoverage::dispatch($coverage_date);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => []
        ]);

    }

    private function getDatesBetween($startDate, $endDate) {
        $dates = array();
        $currentDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        while ($currentDate <= $endDate) {
            $dates[] = date('Y-m-d', $currentDate);
            $currentDate = strtotime('+1 day', $currentDate);
        }

        return $dates;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $icm_coverage = IcmCoverage::find($id);

        ExecuteCoverage::dispatch($icm_coverage->coverage_date);

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => []
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
