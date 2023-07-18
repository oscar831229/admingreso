<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['code', 'name', 'address', 'phone', 'state', 'user_created', 'user_updated'];

    public static function getAllBusiness(){

        $querySQL = "SELECT
                s.id,
                s.code,
                s.name,
                s.address, 
                s.phone,
                s.created_at,
                u.name as user_created, 
                s.state
            FROM stores AS s
            INNER JOIN users AS u ON u.id = s.user_created";

        return \DB::select($querySQL, []);

    }
}
