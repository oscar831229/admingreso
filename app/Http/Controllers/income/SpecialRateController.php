<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Income\IcmSpecialRate;

class SpecialRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validities = [['2023'=>'2023']];

        $months = [
            '01' => [
                'code' => '1',
                'name_title' => 'Enero',
                'name_class' => 'enero'
            ],[
                'code' => '2',
                'name_title' => 'Febrero',
                'name_class' => 'febrero'
            ],[
                'code' => '3',
                'name_title' => 'Marzo',
                'name_class' => 'marzo'
            ],[
                'code' => '4',
                'name_title' => 'Abril',
                'name_class' => 'abril'
            ],[
                'code' => '5',
                'name_title' => 'Mayo',
                'name_class' => 'mayo'
            ],[
                'code' => '6',
                'name_title' => 'Junio',
                'name_class' => 'junio'
            ],[
                'code' => '7',
                'name_title' => 'Julio',
                'name_class' => 'julio'
            ],[
                'code' => '8',
                'name_title' => 'Agosto',
                'name_class' => 'agosto'
            ],[
                'code' => '9',
                'name_title' => 'Septiembre',
                'name_class' => 'septiembre'
            ],[
                'code' => '10',
                'name_title' => 'Octubre',
                'name_class' => 'octubre'
            ],[
                'code' => '11',
                'name_title' => 'Noviembre',
                'name_class' => 'nombiembre'
            ],[
                'code' => '12',
                'name_title' => 'Diciembre',
                'name_class' => 'diciembre'
            ]
        ];

        $year_end = date('Y');
        $specialties = [];
        return view('income.special-rates.index', compact('validities', 'months', 'specialties', 'year_end'));

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

        $request->validate([
            'year' => 'required',
            'date' => 'required'
        ]);

        IcmSpecialRate::create([
            'year'         => $request->year,
            'date'         => $request->date,
            'name'         => 'Dias tarifa alta',
            'description'  => 'Dias tarifa alta',
            'user_created' => 1
        ]);

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
    public function show($year)
    {
        # Validamos dias temporadas altas
        $days = IcmSpecialRate::where(['year' => $year])->get();

        if($days->count() == 0){
            $all_days = getHighSeasonDays($year);
            foreach ($all_days as $key => $day) {
                if($day['is_high_season']){
                    IcmSpecialRate::create([
                        'year'         => $year,
                        'date'         => $day['day'],
                        'name'         => 'Dias tarifa alta',
                        'description'  => 'Dias tarifa alta',
                        'user_created' => 1
                    ]);
                }
            }
            $days = IcmSpecialRate::where(['year' => $year])->get();
        }

        $holidays[$year] = [];
        $appStoragePath = storage_path('holidays/'.$year.'.php');
        if(file_exists($appStoragePath)){
            require $appStoragePath;
        }

        $response_days = [];
        $days_general  = [];

        # Festivos
        foreach ($holidays[$year] as $day_value => $day_name) {
            if(!isset($days_general[$day_value])){
                $response_days[] = [
                    'id'           => 0,
                    'date'       => $day_value,
                    'name'       => $day_name,
                    'is_festive' => 1,
                    'class'      => 'day-success'
                ];
                $days_general[$day_value] = true;
            }
        }

        # Dias temporada alta viernes, sabado, domingo
        foreach ($days as $key => $value) {
            $date = str_replace('-', '', $value['date']);
            if(!isset($days_general[$date])){
                $response_days[] = [
                    'id'         => $value['id'],
                    'date'       => $date,
                    'name'       => $value['name'],
                    'is_festive' => 0,
                    'class'      => 'day-primary'
                ];
                $days_general[$date] = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $response_days
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

        $day_high_season = IcmSpecialRate::find($id);
        $user_id = auth()->user()->id;
        $day_high_season->update(['user_updated' => $user_id]);
        $day_high_season->delete();

        return response()->json([
            'success'  => true,
            'message' => '',
            'data'    => []
        ]);

    }
}
