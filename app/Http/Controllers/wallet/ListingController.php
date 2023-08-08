<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Common\Definition;

class ListingController extends Controller
{

    private $listings = [
        'stores',
        'electrical_pockets'
    ]; 


    public function index(){

        $request = request();
        
        # Consultamos definiciones
        $definition = Definition::where(['code' => $request->table])->first();

        if(!$definition){

            $detail_definitions = [];

            # Validamos si no es definición tablas permitidas
            if(in_array($request->table, $this->listings)){
                $table_exists = \Schema::hasTable($request->table);
                if($table_exists){
                    $detail_definitions = \DB::select("SELECT * FROM {$request->table}", []);
                }
            }
            
        }else {
            $definition = $definition->detaildefinitions()->toArray();
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $detail_definitions
        ]);

    }


}
