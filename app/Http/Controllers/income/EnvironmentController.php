<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Amadeus\Room;
use App\Models\Income\IcmEnvironment;

class EnvironmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        # Migrar ambientes
        $rooms = Room::all();
        foreach ($rooms as $key => $room) {
            $environment = IcmEnvironment::find($room->id);
            if(!$environment){
                $envinroment = new IcmEnvironment;
                $envinroment->id = $room->id;
                $envinroment->name = $room->nombre;
                $envinroment->state = $room->estado;
                $envinroment->user_created = 1;
                $envinroment->save();
            }else{
                $environment->update([
                    'name' => $room->nombre,
                    'state' => $room->estado
                ]);
            }

        }

        $environments = IcmEnvironment::all();

        return view('income.environments.index', compact('environments'));

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
