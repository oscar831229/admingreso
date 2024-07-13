<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmAgreementTypeIncome extends Model
{
    protected $fillable = ['icm_agreement_id', 'icm_types_income_id', 'icm_affiliate_category_id', 'state', 'user_created', 'user_updated' ];
}
