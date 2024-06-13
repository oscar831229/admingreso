<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmCustomer extends Model
{
    protected $fillable = [
        'document_number',
        'document_type',
        'first_name',
        'second_name',
        'first_surname',
        'second_surname',
        'birthday_date',
        'gender',
        'icm_municipality_id',
        'address',
        'phone',
        'email',
        'type_regime_id',
        'type_liability_id',
        'tax_detail_id',
        'type_organization_id',
        'icm_affiliate_category_id',
        'user_created',
        'user_updated'
    ];
}
