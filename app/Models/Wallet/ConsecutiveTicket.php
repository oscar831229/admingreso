<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class ConsecutiveTicket extends Model
{

    protected $fillable = ['prefix','initial_consecutive','final_consecutive','current_consecutive','date_from','date_to','state','observation','user_created','user_updated'];

    public static function getAllConsecutiveTickets(){

        $querySQL = "SELECT
                s.id,
                IFNULL(s.prefix, '') AS prefix,
                s.initial_consecutive,
                s.final_consecutive, 
                s.current_consecutive,
                s.date_from,
                s.date_to,
                s.state,
                s.created_at,
                u.name as user_created, 
                s.state
            FROM consecutive_tickets AS s
            INNER JOIN users AS u ON u.id = s.user_created";

        return \DB::select($querySQL, []);

    }
    
}
