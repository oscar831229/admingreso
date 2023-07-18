<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class WalletUserElectronicPocket extends Model
{
    
    protected $fillable = ['electronic_pocket_id', 'balance', 'wallet_user_id', 'last_movement_date', 'user_created', 'user_updated'];

}
