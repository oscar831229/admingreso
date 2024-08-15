<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmPoliticalAffiliate extends Model
{

    protected $fillable = [
        'type_register',
        'icm_income_item_id',
        'relationship',
        'type_link',
        'type_sublink',
        'affiliated_type_document',
        'affiliated_document',
        'affiliated_name',
        'document_type',
        'document_number',
        'first_surname',
        'second_surname',
        'first_name',
        'second_name',
        'birthday_date',
        'gender',
        'icm_affiliate_category_id',
        'nit_company_affiliates',
        'name_company_affiliates',
        'political_date',
        'user_created',
        'user_updated'
    ];

}
