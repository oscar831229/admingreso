<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmEnvironmentIcmMenuItem extends Model
{

    protected $fillable = ['value', 'state'];

    public static function getEnvironmentMenusItems($environment_id){

        return \DB::table('icm_environment_icm_menu_items AS ieimi')
            ->selectRaw("ieimi.id, imi.name")
            ->join('icm_menu_items AS imi', 'imi.id', 'ieimi.icm_menu_item_id')
            ->where('ieimi.icm_environment_id', '=', $environment_id)
            ->where('ieimi.state', '=', 'A')
            ->orderBy('imi.name', 'asc')
            ->get();

    }

    public function icm_menu_item(){
        return $this->belongsTo('App\Models\Income\IcmMenuItem');
    }


}
