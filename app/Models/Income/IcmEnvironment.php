<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmEnvironment extends Model
{
    protected $fillable = ['name', 'state', 'user_created', 'user_updated'];

    public static function getIncomeServices($environment_id, $icm_environment){

        /**
         * $environment_id == 0 -> Proviene la consuta de paramtrización de convenios
         */
        $control = $icm_environment->id == $environment_id || $environment_id == 0? 'A' : 'P';

        if($control == 'A'){

            $statement =  \DB::table('icm_environments AS ie')
                ->selectRaw("
                    ieii.*,
                    ie.name AS icm_environment_name,
                    'ADULTOS' AS income_type
                ")
                ->join('icm_income_items AS ieii', 'ieii.icm_environment_id', '=', 'ie.id')
                ->where(['ieii.state' => 'A'])
                ->orderBy('ie.id', 'asc')
                ->orderBy('ieii.name', 'asc');

            if(!empty($environment_id)){
                $statement->where(['ie.id' => $environment_id]);
            }

        } else {

            $statement =  \DB::table('icm_environments AS ie')
                ->selectRaw("
                    ieii.*,
                    ie.name AS icm_environment_name,
                    'ADULTOS' AS income_type
                ")
                ->join('icm_income_items AS ieii', 'ieii.icm_environment_id', '=', 'ie.id')
                ->join('icm_environtment_icm_income_items AS ieiii', 'ieiii.icm_income_item_id', '=', 'ieii.id')
                ->where(['ieii.state' => 'A'])
                ->orderBy('ie.id', 'asc')
                ->orderBy('ieii.name', 'asc');

            if(!empty($environment_id)){
                $statement->where(['ie.id' => $environment_id]);
            }

            $statement->where(['ieiii.icm_environment_id' => $icm_environment->id]);

        }

        return $statement->get();

    }


    public function icm_environment_icm_menu_items(){
        return $this->hasMany('App\Models\Income\IcmEnvironmentIcmMenuItem');
    }

    public function getPluckItems(){

        return \DB::table('icm_environments AS ie')
            ->selectRaw("
                ieimi.id,
                imi.name
            ")
            ->join('icm_environment_icm_menu_items AS ieimi', 'ieimi.icm_environment_id', '=', 'ie.id')
            ->join('icm_menu_items AS imi', 'imi.id', '=', 'ieimi.icm_menu_item_id')
            ->where(['ie.id' => $this->id])
            ->orderBy('imi.name', 'asc')->get()->pluck('name', 'id');
    }
}
