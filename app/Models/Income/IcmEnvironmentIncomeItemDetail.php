<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmEnvironmentIncomeItemDetail extends Model
{
    protected $fillable = [
        'icm_environment_income_item_id',
        'icm_types_income_id',
        'icm_affiliate_category_id',
        'icm_rate_type_id',
        'value',
        'state',
        'user_created',
        'user_updated'
    ];

    public function icm_types_income(){
        return $this->belongsTo('App\Models\Income\IcmTypesIncome');
    }

    public function icm_affiliate_category(){
        return $this->belongsTo('App\Models\Income\IcmAffiliateCategory');
    }

    public function icm_rate_type(){
        return $this->belongsTo('App\Models\Income\IcmRateType');
    }






}
