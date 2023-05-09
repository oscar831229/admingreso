<?php

namespace App\Http\Controllers\His\Scheduling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\His\Scheduling\HisMedicalScheduling;

class ListingController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:his-schedule-listings-index', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = request()->query();

        $table = isset($params['table']) ? $params['table'] : '';
        $year = isset($params['year']) ? $params['year'] : date('Y');

        $data = [];
        
        switch ($table) {
            case 'specialty_code':
                $data = HisMedicalScheduling::getSpecialty($year);
                break;
            case 'professional_code':
                $data = HisMedicalScheduling::getProfessional($year);
                break;

            default:
                # code...
                break;
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $data
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
        //
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
