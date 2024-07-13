<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmLiquidacionDetailRevision extends Model
{

    protected $fillable = [
        'icm_coverage_id',
        'step',
        'icm_liquidation_service_id',
        'icm_liquidation_detail_id',
        'is_processed_affiliate',
        'type_register',
        'relationship',
        'type_link',
        'affiliated_type_document',
        'affiliated_document_number',
        'affiliated_name',
        'document_type',
        'document_number',
        'first_name',
        'second_name',
        'first_surname',
        'second_surname',
        'business_name',
        'birthday_date',
        'gender',
        'address',
        'icm_municipality_id',
        'phone',
        'email',
        'code_seac',
        'icm_income_item_id',
        'icm_income_item_code',
        'infrastructure_code',
        'liquidation_date',
        'nit_company_affiliates',
        'name_company_affiliates',
        'icm_types_income_id',
        'icm_affiliate_category_id',
        'category_presented_code',
        'total',
        'icm_type_subsidy_id',
        'subsidy',
        'number_places',
        'icm_family_compensation_fund_id',
        'system_names',
        'icm_liquidation_id',
        'billing_prefix',
        'consecutive_billing',
        'user_created',
        'user_updated'
    ];
}
