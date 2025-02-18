<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmPaymentMethod extends Model
{
    protected $fillable = ['name', 'type_payment_method', 'redeban_operation', 'wallet_pocket', 'state', 'user_created', 'user_updated'];
}
