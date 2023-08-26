<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class HistoricMovementTicketHolder extends Model
{
    protected $fillable = ['movement_id', 'wallet_user_ticket_id', 'number_ticket', 'value', 'state'];
}
