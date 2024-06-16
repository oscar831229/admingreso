<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmLiquidation extends Model
{
    protected $fillable = [
        'uuid',
        'sales_icm_environment_id',
        'icm_environment_id',
        'document_type',
        'document_number',
        'first_name',
        'second_name',
        'first_surname',
        'second_surname',
        /*'birthday_date',
        'gender', */
        'total',
        'state',
        'icm_resolution_id',
        'billing_prefix',
        'consecutive_billing',
        'user_created',
        'user_updated'
    ];

    public static function getDetailsServices($icm_liquidacion_id){
        $querySQL = "SELECT
                    ils.id,
                    ils.applied_rate_code,
                    ieii.name AS icm_environment_income_item_name,
                    ils.number_places,
                    ils.base,
                    ils.iva,
                    ils.impoconsumo,
                    ils.total,
                    ils.icm_type_subsidy_id,
                    ils.discount,
                    ils.subsidy
            FROM `icm_liquidation_services` AS ils
            INNER JOIN icm_income_items AS ieii ON ieii.id = ils.icm_income_item_id
            INNER JOIN icm_environment_icm_menu_items AS ieimi ON ieimi.id = ils.icm_environment_icm_menu_item_id
            INNER JOIN icm_menu_items AS imi ON imi.id = ieimi.icm_menu_item_id
            WHERE ils.icm_liquidation_id = ? ";

        return \DB::select($querySQL, [$icm_liquidacion_id]);

    }

    public function icm_liquidation_services(){
        return $this->hasMany('App\Models\Income\IcmLiquidationService');
    }

    public function icm_liquidation_payments(){
        return $this->hasMany('App\Models\Income\IcmLiquidationPayment');
    }

    public function icm_customer(){
        return $this->belongsTo('App\Models\Income\IcmCustomer', 'document_number', 'document_number');
    }

}
