<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmEnvirontmentIcmIncomeItem extends Model
{
    protected $fillable = ['icm_environment_id', 'icm_income_item_id', 'icm_environment_icm_menu_item_id', 'state', 'user_created', 'user_updated'];

    public function icm_environment(){
        return $this->belongsTo('App\Models\Income\IcmEnvironment');
    }

    public function icm_income_item(){
        return $this->belongsTo('App\Models\Income\IcmIncomeItem');
    }
}
