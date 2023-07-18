<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class ElectricalPocket extends Model
{
    protected $fillable = ['code', 'name', 'description', 'main', 'user_created', 'user_updated'];
}
