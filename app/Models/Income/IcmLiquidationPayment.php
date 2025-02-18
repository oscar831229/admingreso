<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmLiquidationPayment extends Model
{
    protected $fillable = ['icm_liquidation_id', 'icm_payment_method_id', 'approval_date', 'approval_number', 'value', 'redeban', 'state', 'user_created', 'user_updated'];
}
