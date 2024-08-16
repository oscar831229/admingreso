<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmMenuItem extends Model
{
    protected $fillable = ['name','requested_name', 'barcode', 'value', 'percentage_iva', 'percentage_impoconsumo', 'state', 'user_created'];
}
