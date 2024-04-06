<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmEnvironmentIncomeItemDetail extends Model
{
    protected $fillable = [
        'icm_environment_income_item_id',
        'types_of_income_id',
        'icm_affiliate_category_id',
        'icm_rate_type_id',
        'value',
        'state',
        'user_created',
        'user_updated'
    ];
}
