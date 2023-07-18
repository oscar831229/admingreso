<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class MovementType extends Model
{
    protected $fillable = ['code', 'name', 'observation', 'nature_movement', 'state', 'user_created', 'user_updated'];

    public static function getAllMovementTypes(){

        $querySQL = "SELECT
                mt.id,
                mt.code,
                mt.name,
                mt.nature_movement,
                mt.state,
                mt.created_at,
                u.name as user_created
            FROM movement_types AS mt
            INNER JOIN users AS u ON u.id = mt.user_created";

        return \DB::select($querySQL, []);

    }


}
