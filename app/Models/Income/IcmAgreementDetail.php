<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmAgreementDetail extends Model
{
    protected $fillable = [
        'icm_agreement_id',
        'icm_environment_income_item_id',
        'icm_rate_type_id',
        'value',
        'state',
        'user_created',
        'user_updated'
    ];
}
