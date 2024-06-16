<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmLiquidationService extends Model
{
    protected $fillable = [
        'icm_liquidation_id',
        'icm_income_item_id',
        'icm_environment_id',
        'icm_environment_icm_menu_item_id',
        'number_places',
        'applied_rate_code',
        'base',
        'percentage_iva',
        'iva',
        'percentage_impoconsumo',
        'impoconsumo',
        'total',
        'user_created',
        'user_updated',
        'general_price',
        'discount',
        'subsidy',
        'icm_type_subsidy_id'
    ];

    public static function getPeopleService($icm_liquidation_service){

        $querySQL = "SELECT
            ild.id,
            ild.document_number,
            CONCAT(IFNULL(ild.first_name, ''), ' ', IFNULL(ild.second_name, ''), ' ', IFNULL(ild.first_surname, ''), ' ', IFNULL(ild.second_surname,'')) AS person_name,
            iti.name AS icm_types_income_name,
            iac.name AS icm_affiliate_category_name,
            IFNULL(ifcf.name, '') AS icm_family_compensation_fund_name
        FROM icm_liquidation_details AS ild
        INNER JOIN `icm_types_incomes` AS iti ON iti.id = ild.icm_types_income_id
        INNER JOIN `icm_affiliate_categories` AS iac ON iac.id = ild.icm_affiliate_category_id
        LEFT JOIN `icm_family_compensation_funds` AS ifcf ON ifcf.id = ild.icm_family_compensation_fund_id
        WHERE ild.icm_liquidation_service_id = ?";

        $people = \DB::select($querySQL, [$icm_liquidation_service->id]);
        return $people;

    }

    public function icm_liquidation_details(){
        return $this->hasMany('App\Models\Income\IcmLiquidationDetail');
    }


}
