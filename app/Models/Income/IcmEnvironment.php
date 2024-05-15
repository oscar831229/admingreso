<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmEnvironment extends Model
{
    protected $fillable = ['name', 'state', 'user_created', 'user_updated'];

    public static function getIncomeServices($environment_id){
        return \DB::table('icm_environments AS ie')
            ->selectRaw("
                ieii.*,
                'ADULTOS' AS income_type
            ")
            ->join('icm_income_items AS ieii', 'ieii.icm_environment_id', '=', 'ie.id')
            ->where(['icm_environment_id' => $environment_id])
            ->get();
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
