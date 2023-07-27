<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class ElectricalPocket extends Model
{
    protected $fillable = ['code', 'name', 'description', 'operation_type', 'minimum_purchase', 'unit_value', 'main', 'user_created', 'user_updated'];

    public static function getAllElectricalPockets(){
        $querySQL = "SELECT
            s.id,
            s.code,
            s.name,
            s.description,
            s.operation_type, 
            IFNULL(s.minimum_purchase, '') AS minimum_purchase,
            IFNULL(s.unit_value, '') AS unit_value, 
            s.created_at,
            u.name as user_created
        FROM electrical_pockets AS s
        INNER JOIN users AS u ON u.id = s.user_created";

        return \DB::select($querySQL, []);
    }
}
