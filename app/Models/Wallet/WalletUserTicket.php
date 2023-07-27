<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class WalletUserTicket extends Model
{
    protected $fillable = ['wallet_user_id', 'movement_id', 'consecutive_ticket_id','number', 'number_ticket', 'value', 'state', 'redemption_date', 'redemption_store_id', 'redemption_user_code', 'user_created', 'user_updated'];


    public static function getEnabledTicket($electrical_pocket_wallet_user_id){

        $querySQL = "SELECT wut.id, wut.number, wut.number_ticket, wut.value, m.cus, m.user_code, wut.created_at 
        FROM movements AS m
        INNER JOIN wallet_user_tickets AS wut ON wut.movement_id = m.id
        WHERE m.electrical_pocket_wallet_user_id = ? AND wut.state = 'P'
        ORDER BY m.id, wut.number";  

        return \DB::select($querySQL, [$electrical_pocket_wallet_user_id]);

    }

}
