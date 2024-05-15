<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmLiquidationDetail extends Model
{
    protected $fillable = [
        'icm_liquidation_service_id',
        'document_type',
        'document_number',
        'first_name',
        'second_name',
        'first_surname',
        'second_surname',
        'icm_types_income_id',
        'icm_affiliate_category_id',
        'category_presented_code',
        'icm_family_compensation_fund_id',
        'nit_company_affiliates',
        'name_company_affiliates',
        'nit_company_agreement',
        'name_company_agreement',
        'icm_agreement_id',
        'icm_liquidation_id',
        'state',
        'user_created',
    ];

    public function icm_liquidation_services(){
        return $this->hasMany('App\Models\Income\IcmLiquidationService');
    }

}
